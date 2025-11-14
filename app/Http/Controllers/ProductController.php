<?php
// App/Http/Controllers/ProductController.php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory; 
use App\Models\Client; // ⬅️ ¡IMPORTACIÓN AGREGADA!
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        $search = $request->input('search');
        $categoryFilter = $request->input('category_id');

        // Búsqueda Global 
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        // Filtro por Categoría
        if ($categoryFilter) {
            $query->where('product_category_id', $categoryFilter); 
        }

        $products = $query->with('category')->orderBy('id', 'desc')->paginate(10);
        
        // 🚨 SOLUCIÓN: Cargar la colección de clientes para la vista Blade
        $categories = ProductCategory::all(); 
        $clients = Client::all(); 

        // Retornar la vista, pasando AMBAS variables
        return view('vistas.productos', compact('products', 'categories', 'clients')); // ⬅️ $clients se pasa aquí
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'weight_grams' => 'nullable|integer|min:0',
            'product_category_id' => 'nullable|exists:product_categories,id', 
        ]);
        
        $product = Product::create($validated);
        
        return Redirect::route('productos.index')->with('success', "Producto '{$product->name}' agregado correctamente.");
    }
    
    public function update(Request $request, Product $producto)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku,' . $producto->id, 
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'weight_grams' => 'nullable|integer|min:0',
            'product_category_id' => 'nullable|exists:product_categories,id',
        ]);
        
        $producto->update($validated);
        
        return Redirect::route('productos.index')->with('success', "Producto '{$producto->name}' actualizado correctamente.");
    }
    
    public function destroy(Product $producto)
    {
        $nombre = $producto->name;
        $producto->delete();
        
        return Redirect::route('productos.index')->with('success', "Producto '{$nombre}' eliminado correctamente.");
    }
}