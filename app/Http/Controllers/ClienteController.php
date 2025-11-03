<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Muestra una lista de todos los clientes.
     */
    public function index()
{
    // Este nombre 'clientes' busca el archivo 'clientes.blade.php'
    // en la carpeta resources/views/
    return view('/vistas/clientes'); 
}
}