<?php
// App/Http/Controllers/ProductCategoryController.php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProductCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'color_code' => 'nullable|string|max:7',
        ]);
        ProductCategory::create($validated);
        
        return Redirect::route('productos.index')->with([
            'success' => 'Categoría de producto creada exitosamente.',
            'open_modal' => 'manageCategoriesModal' // Reabre el modal de gestión
        ]);
    }

    public function update(Request $request, ProductCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $category->id,
            'color_code' => 'nullable|string|max:7',
        ]);

        $category->update($validated);

        return Redirect::route('productos.index')->with([
            'success' => 'Categoría de producto actualizada correctamente.',
            'open_modal' => 'manageCategoriesModal'
        ]);
    }

    public function destroy(ProductCategory $category)
    {
        $category->delete();

        return Redirect::route('productos.index')->with([
            'success' => 'Categoría de producto eliminada correctamente.',
            'open_modal' => 'manageCategoriesModal'
        ]);
    }
}