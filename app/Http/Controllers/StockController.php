<?php
// App/Http/Controllers/StockController.php

namespace App\Http\Controllers;

use App\Models\Product; // Usaremos Product para listar
use App\Models\ProductCategory; // Para filtros
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Muestra el listado de productos con su stock actual (READ).
     */
    public function index(Request $request)
    {
        $query = Product::query();
        $search = $request->input('search');
        $categoryFilter = $request->input('category_id');

        // Búsqueda Global (SKU, Nombre)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }
        
        // Filtro por Categoría
        if ($categoryFilter) {
            $query->where('product_category_id', $categoryFilter); 
        }

        // Cargamos las relaciones stock y category
        $products = $query->with(['stock', 'category'])->orderBy('name', 'asc')->paginate(15);
        
        // Cargamos todas las categorías para los filtros
        $categories = ProductCategory::all();

        // Asume que la vista se llama 'vistas.stock'
        return view('vistas.stock', compact('products', 'categories'));
    }
    
    /**
     * Actualiza la cantidad de stock para un producto (UPDATE).
     * Nota: Recibe el ID del PRODUCTO, no del stock.
     */
    public function update(Request $request, Product $producto)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        // Encuentra o crea el registro de stock
        $stock = $producto->stock()->updateOrCreate(
            ['product_id' => $producto->id],
            ['quantity' => $request->quantity]
        );

        return redirect()->route('stock.index')->with('success', "Stock de '{$producto->name}' actualizado a {$stock->quantity}.");
    }

    // Omitimos store, create, show, edit, destroy ya que el stock se gestiona desde Product
}