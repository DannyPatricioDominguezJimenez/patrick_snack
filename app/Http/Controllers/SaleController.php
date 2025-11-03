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

class SaleController extends Controller
{
    /**
     * Muestra la lista de ventas con filtros avanzados (READ).
     */
    public function index(Request $request)
    {
        $query = Sale::query();
        
        // Cargar todas las colecciones necesarias para los filtros y la vista
        $clients = Client::orderBy('nombre')->get();
        $clientCategories = ClientCategory::all();
        $products = Product::orderBy('name')->get();

        // 1. FILTRO POR RANGO DE FECHAS (Desde y Hasta)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            $query->whereBetween('sale_date', [$startDate, $endDate]);
        } else {
            // Por defecto, mostrar ventas del último mes o el día actual si no hay filtro
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
        
        // 4. FILTRO POR PRODUCTOS (CHECKBOXES - FILTRO MÁS COMPLEJO)
        if ($request->filled('product_ids')) {
            $productIds = $request->input('product_ids');
            $query->whereHas('details', function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds);
            });
        }

        // Ejecutar la consulta con paginación
        $sales = $query->with(['client', 'details.product']) // Cargar relaciones necesarias
                       ->orderBy('sale_date', 'desc')
                       ->paginate(10)
                       ->appends($request->query());

        return view('vistas.ventas', compact('sales', 'clients', 'clientCategories', 'products', 'startDate', 'endDate'));
    }

    /**
     * Muestra el formulario de creación (CREATE).
     * Usaremos este método para cargar datos en el modal de Creación.
     */
    public function create()
    {
        // Esto generalmente no se usa si el formulario está en un modal de la vista 'index'
        return Redirect::route('ventas.index');
    }


    /**
     * Almacena una nueva venta (CREATE) y maneja el stock.
     * Esta es una de las funciones más críticas.
     */
    public function store(Request $request)
    {
        // 1. Validación de la Venta (Encabezado)
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'sale_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        
        $totalAmount = 0;
        
        // Usamos una Transacción para asegurar que el stock se actualice si la venta se registra, o viceversa.
        try {
            DB::beginTransaction();

            // 2. Crear el Encabezado de Venta (Inicial)
            $sale = Sale::create([
                'client_id' => $request->client_id,
                'sale_date' => $request->sale_date,
                'total_amount' => 0, // Inicializamos en 0
                'status' => 'Pagada', // Asumimos pagada al crear
            ]);

            $details = [];

            // 3. Procesar Detalles y Stock
            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);
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

            // 4. Registrar todos los Detalles de Venta en la DB
            $sale->details()->createMany($details);

            // 5. Actualizar el Total Final en el Encabezado
            $sale->update(['total_amount' => $totalAmount]);

            DB::commit();

            return Redirect::route('ventas.index')->with('success', 'Venta registrada y stock actualizado correctamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            // Redirige manteniendo los errores de stock en la sesión
            return Redirect::back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            // Error general de DB o lógica
            return Redirect::back()->withInput()->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualiza una venta existente (UPDATE) y gestiona el stock.
     * Esta función requiere lógica para devolver stock viejo y descontar stock nuevo.
     */
    public function update(Request $request, Sale $venta)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'sale_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        
        $newTotalAmount = 0;
        $oldDetails = $venta->details->keyBy('product_id'); // Detalle viejo indexado por product_id

        try {
            DB::beginTransaction();

            // 1. DEVOLVER EL STOCK VIEJO Y ELIMINAR DETALLES VIEJOS
            foreach ($venta->details as $detail) {
                // Devolver la cantidad vendida al stock del producto
                $detail->product->stock->increment('quantity', $detail->quantity);
            }
            $venta->details()->delete(); // Eliminar todos los detalles viejos

            $newDetails = [];

            // 2. PROCESAR DETALLES NUEVOS Y DESCONTAR STOCK NUEVO
            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);
                $quantity = $item['quantity'];

                // Verificar stock para la NUEVA venta (el stock viejo ya se devolvió)
                $stock = $product->stock->quantity ?? 0;
                if ($stock < $quantity) {
                     // Si no hay stock, deshacemos todo
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
     * Elimina una venta (DELETE) y devuelve el stock.
     */
    public function destroy(Sale $venta)
    {
        try {
            DB::beginTransaction();
            
            // 1. Devolver Stock
            foreach ($venta->details as $detail) {
                $detail->product->stock->increment('quantity', $detail->quantity);
            }

            // 2. Eliminar la Venta (Los detalles se eliminan por 'onDelete: cascade')
            $venta->delete();

            DB::commit();

            return Redirect::route('ventas.index')->with('success', 'Venta eliminada y stock devuelto correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }
    
    /**
     * Genera la Nota de Venta (Factura o Invoice).
     */
    public function generateInvoice(Sale $sale)
    {
        // 💡 NOTA: Aquí integrarías una librería como DomPDF o Snappy
        // para renderizar una vista Blade como PDF.
        
        // Simulación de respuesta HTML o PDF
        return view('invoices.sale_note', compact('sale'));
        
        /*
        // Ejemplo de generación de PDF con DomPDF
        $pdf = PDF::loadView('invoices.sale_note', compact('sale'));
        return $pdf->download('Nota_Venta_' . $sale->id . '.pdf');
        */
    }
    
    // show y edit no son necesarios, ya que usamos modales en la vista 'index'
}