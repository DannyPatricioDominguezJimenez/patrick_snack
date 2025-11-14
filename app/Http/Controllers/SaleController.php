<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use App\Models\ClientCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; 

class SaleController extends Controller
{
    /**
     * Muestra la lista de ventas con filtros avanzados (READ).
     */
    public function index(Request $request)
    {
        $query = Sale::query();
        
        // Cargar colecciones necesarias para los SELECTS y la vista
        $clients = Client::orderBy('nombre')->get();
        $clientCategories = ClientCategory::all();
        $products = Product::orderBy('name')->get();

        // Para el modal de Detalle (READ): Cargamos clients con category para el JS
        $clientsWithCategory = Client::with('category')->get(); 

        // 1. FILTRO POR RANGO DE FECHAS (Desde y Hasta)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('sale_date', [$startDate, $endDate]);
        } else {
            // Por defecto, mostrar ventas del último mes
            $query->whereDate('sale_date', '>=', now()->subDays(30));
        }

        // 2. FILTRO POR CLIENTE INDIVIDUAL
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // 3. FILTRO POR CATEGORÍA DE CLIENTE
        if ($request->filled('client_category_id')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('client_category_id', $request->client_category_id);
            });
        }
        
        // 4. FILTRO POR PRODUCTOS
        if ($request->filled('product_ids')) {
            $productIds = array_filter($request->input('product_ids')); 
            
            if (!empty($productIds)) {
                $query->whereHas('details', function ($q) use ($productIds) {
                    $q->whereIn('product_id', $productIds);
                });
            }
        }

        // Ejecutar la consulta con paginación, cargando las relaciones necesarias
        $sales = $query->with(['client.category', 'details.product']) 
                       ->orderBy('sale_date', 'desc')
                       ->paginate(10)
                       ->appends($request->query());

        return view('vistas.ventas', compact(
            'sales', 
            'clients', 
            'clientCategories', 
            'products', 
            'startDate', 
            'endDate',
            'clientsWithCategory'
        ));
    }

    /**
     * Almacena una nueva venta (CREATE) y maneja el stock.
     */
    public function store(Request $request)
    {
        // 1. Validación (Añadidas reglas para payment_method y status)
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:Efectivo,Transferencia,Credito',
            'status' => 'required|in:Pagada,Pendiente',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        
        $totalAmount = 0;
        
        try {
            DB::beginTransaction();

            // 2. Crear el Encabezado de Venta (Guardando los nuevos campos)
            $sale = Sale::create([
                'client_id' => $request->client_id,
                'sale_date' => $request->sale_date,
                'total_amount' => 0,
                'payment_method' => $request->payment_method, // ⬅️ Guardar método
                'status' => $request->status, // ⬅️ Guardar estado
            ]);

            $details = [];

            // 3. Procesar Detalles y Stock
            foreach ($request->products as $item) {
                $product = Product::with('stock')->find($item['product_id']);
                $quantity = $item['quantity'];
                
                // 3a. VERIFICAR STOCK
                $stock = $product->stock->quantity ?? 0;
                if ($stock < $quantity) {
                    throw ValidationException::withMessages(['stock_error' => "Stock insuficiente para {$product->name}. Disponible: {$stock}"]);
                }

                $unitPrice = $product->price;
                $subtotal = $unitPrice * $quantity;
                $totalAmount += $subtotal;

                // 3b. Descontar Stock
                $product->stock->decrement('quantity', $quantity);

                // 3c. Registrar Detalle
                $details[] = [
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 4. Registrar todos los Detalles y Actualizar Total
            $sale->details()->createMany($details);
            $sale->update(['total_amount' => $totalAmount]);

            DB::commit();

            return Redirect::route('ventas.index')->with('success', 'Venta registrada y stock actualizado correctamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            return Redirect::back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withInput()->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualiza una venta existente (UPDATE) y gestiona el stock.
     */
    public function update(Request $request, Sale $venta)
    {
        // Validación (Añadidas reglas para payment_method y status)
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:Efectivo,Transferencia,Credito',
            'status' => 'required|in:Pagada,Pendiente',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        
        $newTotalAmount = 0;

        try {
            DB::beginTransaction();

            // 1. DEVOLVER EL STOCK VIEJO Y ELIMINAR DETALLES VIEJOS
            foreach ($venta->details as $detail) {
                $detail->product->stock->increment('quantity', $detail->quantity);
            }
            $venta->details()->delete();

            $newDetails = [];

            // 2. PROCESAR DETALLES NUEVOS Y DESCONTAR STOCK NUEVO
            foreach ($request->products as $item) {
                $product = Product::with('stock')->find($item['product_id']);
                $quantity = $item['quantity'];

                // Verificar stock para la NUEVA venta 
                $stock = $product->stock->quantity ?? 0;
                if ($stock < $quantity) {
                    throw ValidationException::withMessages(['stock_error' => "Stock insuficiente para {$product->name}. Disponible: {$stock}"]);
                }

                $unitPrice = $product->price;
                $subtotal = $unitPrice * $quantity;
                $newTotalAmount += $subtotal;

                // Descontar Stock Nuevo
                $product->stock->decrement('quantity', $quantity);

                // Registrar Nuevo Detalle
                $newDetails[] = [
                    'sale_id' => $venta->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 3. Registrar los Detalles Nuevos y Actualizar Encabezado
            $venta->details()->createMany($newDetails);
            $venta->update([
                'client_id' => $request->client_id,
                'sale_date' => $request->sale_date,
                'total_amount' => $newTotalAmount,
                'payment_method' => $request->payment_method, // ⬅️ Actualizar método
                'status' => $request->status, // ⬅️ Actualizar estado
            ]);

            DB::commit();

            return Redirect::route('ventas.index')->with('success', 'Venta actualizada y stock reajustado correctamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            return Redirect::back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withInput()->with('error', 'Error al actualizar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza solo el estado de la venta mediante AJAX (Usado para el select en la tabla).
     */
    public function updateStatus(Request $request, Sale $sale)
    {
        $request->validate([
            'status' => 'required|in:Pagada,Pendiente',
        ]);

        try {
            $sale->update(['status' => $request->status]);
            return response()->json(['success' => true, 'message' => 'Estado actualizado.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina una venta (DELETE) y devuelve el stock.
     */
    public function destroy(Sale $venta)
    {
        try {
            DB::beginTransaction();
            
            // 1. Devolver Stock
            foreach ($venta->details as $detail) {
                $detail->product->stock()->increment('quantity', $detail->quantity); 
            }

            // 2. Eliminar la Venta
            $venta->delete();

            DB::commit();

            return Redirect::route('ventas.index')->with('success', 'Venta eliminada y stock devuelto correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }
    
    /**
     * Genera la Nota de Venta (Vista Previa).
     */
    public function generateInvoice(Sale $sale)
    {
        $sale->load(['client.category', 'details.product']); 
        
        $pdf = Pdf::loadView('vistas.sale_note', compact('sale')); 
        
        $pdfBase64 = base64_encode($pdf->output());

        return view('vistas.preview', [ 
            'sale' => $sale,
            'pdfBase64' => $pdfBase64,
        ]);
    }
    
    /**
     * Descarga la Nota de Venta como PDF.
     */
    public function downloadInvoice(Sale $sale)
    {
        $sale->load(['client', 'details.product']);
        
        $pdf = Pdf::loadView('vistas.sale_note', compact('sale')); 
        
        $filename = 'Nota_Venta_' . $sale->id . '_' . $sale->client->nombre . '.pdf';
        
        return $pdf->download($filename);
    }
}