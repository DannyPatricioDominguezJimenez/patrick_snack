<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Muestra una lista del estado de stock/inventario.
     */
    public function index()
    {
        // En una aplicación real, aquí recuperarías la información del stock.
        
        // Retorna la vista 'stock.blade.php'
        return view('/vistas/stock');
    }
}