<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VentaController extends Controller
{
    /**
     * Muestra la lista de ventas.
     */
    public function index()
    {
        // Aquí recuperarías la información de las ventas.
        
        // Retorna la vista 'ventas.blade.php'
        return view('vistas/ventas');
    }
}