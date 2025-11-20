<?php
// App/Http/Controllers/ClientController.php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException; // Necesario para la excepción

class ClientController extends Controller
{
    /**
     * Helper para formatear la cédula/RUC (13 ceros si está vacía)
     */
    protected function formatCedula(?string $cedula): string
    {
        // Si está vacío, rellenar con 13 ceros, asumiendo RUC/Cédula largo como estándar.
        if (empty($cedula)) {
            return str_pad('', 13, '0'); 
        }
        // Si no está vacío, devolver el valor validado.
        return $cedula; 
    }
    
    /**
     * Mostrar lista de clientes con filtros y categorías.
     */
    public function index(Request $request)
    {
        $query = Client::query();
        $search = $request->input('search');
        $categoryFilter = $request->input('category_id'); 
        
        $categories = ClientCategory::all();

        // 1. Búsqueda Global 
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cedula', 'like', '%' . $search . '%')
                  ->orWhere('nombre', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('telefono', 'like', '%' . $search . '%');
            });
        }
        
        // 2. Filtro por Categoría
        if ($categoryFilter) {
            $query->where('client_category_id', $categoryFilter); 
        }

        $clientes = $query->with('category')->orderBy('id', 'desc')->paginate(10);

        return view('vistas.clientes', compact('clientes', 'categories'));
    }

    /**
     * Crear un nuevo cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // 🚨 CAMBIO CLAVE: Mínimo 10 y Máximo 13 dígitos
            'cedula' => 'nullable|string|min:10|max:13|unique:clients,cedula', 
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'client_category_id' => 'nullable|exists:client_categories,id',
        ]);
        
        // Si se ingresaron 10 dígitos, el formato será 10. Si fueron 13, serán 13.
        // La función formatCedula se mantiene para evitar nulls, pero no rellena si hay valor.
        $validated['cedula'] = $this->formatCedula($validated['cedula'] ?? null);

        $client = Client::create($validated);
        
        return Redirect::route('clientes.index')->with('success', "Cliente '{$client->nombre}' agregado correctamente.");
    }

    /**
     * Actualizar un cliente existente.
     */
    public function update(Request $request, Client $cliente)
    {
        $validated = $request->validate([
            // 🚨 CAMBIO CLAVE: Mínimo 10 y Máximo 13 dígitos
            'cedula' => ['nullable', 'string', 'min:10', 'max:13', Rule::unique('clients', 'cedula')->ignore($cliente->id)],
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'client_category_id' => 'nullable|exists:client_categories,id',
        ]);
        
        $validated['cedula'] = $this->formatCedula($validated['cedula'] ?? null);

        $cliente->update($validated);

        return Redirect::route('clientes.index')->with('success', "Cliente '{$cliente->nombre}' actualizado correctamente.");
    }

    /**
     * Eliminar un cliente.
     */
    public function destroy(Client $cliente)
    {
        $cliente->delete();

        return Redirect::route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }
}