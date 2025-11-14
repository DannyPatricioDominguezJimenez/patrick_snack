<?php
// App/Http/Controllers/ClientController.php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use Illuminate\Validation\Rule;

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
        $categoryFilter = $request->input('client_category_id'); // Corregido el nombre de input
        
        // Cargar colecciones necesarias para la vista
        $categories = ClientCategory::all();

        // 1. Búsqueda Global (Cédula, Nombre, Email, Teléfono)
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

        // Retornar la vista, pasando AMBAS variables
        return view('vistas.clientes', compact('clientes', 'categories'));
    }

    /**
     * Crear un nuevo cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // 🚨 CAMBIO CLAVE: Validación estricta de 13 dígitos
            'cedula' => 'nullable|string|digits:13|unique:clients,cedula', 
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'client_category_id' => 'nullable|exists:client_categories,id',
        ]);
        
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
            // 🚨 CAMBIO CLAVE: Validación estricta de 13 dígitos
            'cedula' => ['nullable', 'string', 'digits:13', Rule::unique('clients', 'cedula')->ignore($cliente->id)],
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