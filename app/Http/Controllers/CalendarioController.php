<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    /**
     * Muestra la vista del calendario.
     */
    public function index()
    {
        // Aquí podrías cargar los eventos desde la base de datos
        
        // Retorna la vista 'calendario.blade.php'
        return view('/vistas/calendario');
    }
}