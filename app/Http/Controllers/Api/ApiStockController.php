<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importante para transacciones seguras

class ApiStockController extends Controller
{
    /**
     * 1. LEER: Para llenar tu tabla en Flutter
     */
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Filtros (Buscador y Categoría)
        $search = $request->input('search');
        $categoryFilter = $request->input('category_id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }
        
        if ($categoryFilter) {
            $query->where('product_category_id', $categoryFilter); 
        }

        // Obtenemos productos con su stock actual
        $products = $query->with(['stock', 'category'])
                          ->orderBy('name', 'asc')
                          ->get();

        return response()->json($products, 200);
    }

    /**
     * 2. EDITAR MANUAL: Para cuando editas directo en la tabla
     * (Esto SOBREESCRIBE el valor. Ej: De 90 pasa a 50)
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $request->validate(['quantity' => 'required|integer|min:0']);

        // UpdateOrCreate: Si no tiene stock, lo crea. Si tiene, lo reemplaza.
        $stock = $product->stock()->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => $request->quantity] // <--- Reemplazo directo
        );

        return response()->json([
            'message' => "Stock corregido manualmente",
            'new_quantity' => $stock->quantity
        ], 200);
    }

    /**
     * 3. SUMAR POR QR: Esta es la función para tu "Plus"
     * (Esto SUMA al valor actual. Ej: 90 + 30 = 120)
     */
    public function addStock(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        // Usamos una transacción para evitar errores si dos personas escanean a la vez
        $result = DB::transaction(function () use ($request, $id) {
            $product = Product::lockForUpdate()->find($id); // Bloquea la fila momentáneamente

            if (!$product) {
                return null;
            }

            // Buscamos el stock o lo iniciamos en 0 si no existe
            $stock = $product->stock()->firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0]
            );

            // AQUI ESTÁ LA MAGIA: Sumamos la cantidad del QR a lo que ya había
            $stock->quantity = $stock->quantity + $request->quantity;
            $stock->save();

            return [
                'product_name' => $product->name,
                'added' => $request->quantity,
                'total' => $stock->quantity
            ];
        });

        if (!$result) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json([
            'message' => "Stock actualizado con éxito",
            'data' => $result
        ], 200);
    }
}