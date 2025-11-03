<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientCategory; // <-- Importante: Importar el modelo de categorías
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Helper para formatear la cédula (10 ceros si está vacía)
     */
    protected function formatCedula(?string $cedula): string
    {
        if (empty($cedula)) {
            return str_pad('', 10, '0'); 
        }
        return str_pad($cedula, 10, '0', STR_PAD_LEFT); 
    }
    
    /**
     * Mostrar lista de clientes con filtros y categorías.
     */
    public function index(Request $request)
    {
        $query = Client::query();
        $search = $request->input('search');
        $categoryFilter = $request->input('category_id'); // <-- Nuevo filtro por ID de categoría

        // 1. Búsqueda Global (Cédula, Nombre, Email, Teléfono)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cedula', 'like', '%' . $search . '%')
                  ->orWhere('nombre', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('telefono', 'like', '%' . $search . '%');
            });
        }
        
        // 2. Filtro por Categoría (Nuevo)
        if ($categoryFilter) {
            // Asegura que solo se filtre si el valor es un ID válido
            $query->where('client_category_id', $categoryFilter); 
        }

        // Cargamos la relación 'category' para evitar N+1 queries en la vista
        $clientes = $query->with('category')->orderBy('id', 'desc')->paginate(10);
        
        // Cargamos todas las categorías para los SELECTs y los Modales
        $categories = ClientCategory::all();

        // Cambié la vista a la que tienes en "vistas"
        return view('vistas.clientes', compact('clientes', 'categories')); // <-- Pasamos $categories
    }

    /**
     * Crear un nuevo cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cedula' => 'nullable|string|digits:10',
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'client_category_id' => 'nullable|exists:client_categories,id', // <-- Nuevo: valida que exista
        ]);
        
        // Formatear cédula antes de guardar
        $validated['cedula'] = $this->formatCedula($validated['cedula'] ?? null);

        Client::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente agregado correctamente.');
    }

    /**
     * Actualizar un cliente existente.
     */
    public function update(Request $request, Client $cliente)
    {
        $validated = $request->validate([
            'cedula' => 'nullable|string|digits:10',
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'client_category_id' => 'nullable|exists:client_categories,id', // <-- Nuevo: valida que exista
        ]);
        
        // Formatear cédula antes de guardar
        $validated['cedula'] = $this->formatCedula($validated['cedula'] ?? null);

        $cliente->update($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Eliminar un cliente.
     */
    public function destroy(Client $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }
}