<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Muestra una lista de todos los productos.
     */
    public function index()
    {
        // En una aplicación real, aquí recuperarías los datos de los productos.
        
        // Retorna la vista 'productos.blade.php'
        return view('/vistas/productos');
    }
}