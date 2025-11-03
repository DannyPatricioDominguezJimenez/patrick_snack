<?php

namespace App\Http\Controllers;

use App\Models\ClientCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ClientCategoryController extends Controller
{
    /**
     * Almacena una nueva categoría.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:client_categories,name',
            'color_code' => 'nullable|string|max:7', // Ejemplo: #RRGGBB
        ]);

        ClientCategory::create($validated);

        // Redirige al índice de clientes, manteniendo el modal de categorías abierto
        return Redirect::route('clientes.index')->with([
            'success' => 'Categoría creada exitosamente.',
            'open_modal' => 'manageCategoriesModal' // Para reabrir el modal de gestión
        ]);
    }

    /**
     * Actualiza la categoría especificada.
     */
    public function update(Request $request, ClientCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:client_categories,name,' . $category->id,
            'color_code' => 'nullable|string|max:7',
        ]);

        $category->update($validated);

        // Redirige al índice de clientes, manteniendo el modal de categorías abierto
        return Redirect::route('clientes.index')->with([
            'success' => 'Categoría actualizada correctamente.',
            'open_modal' => 'manageCategoriesModal' // Para reabrir el modal de gestión
        ]);
    }

    /**
     * Elimina la categoría especificada.
     */
    public function destroy(ClientCategory $category)
    {
        // NOTA: La relación en la migración ya garantiza que los clientes
        // que usaban esta categoría simplemente tendrán client_category_id = null.
        $category->delete();

        // Redirige al índice de clientes, manteniendo el modal de categorías abierto
        return Redirect::route('clientes.index')->with([
            'success' => 'Categoría eliminada correctamente.',
            'open_modal' => 'manageCategoriesModal' // Para reabrir el modal de gestión
        ]);
    }

    // Los métodos index, create, show, edit no son necesarios ya que usamos modales en la vista de clientes
}