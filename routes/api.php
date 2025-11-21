<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiStockController;

// 1. Rutas de Autenticación (Públicas)
// 🚨 CORRECCIÓN: Quitamos la barra inicial de '/login' -> 'login'
Route::post('/login', [ApiAuthController::class, 'login']); 

// 2. Rutas Protegidas (Requiere Token)
Route::middleware('auth:sanctum')->group(function () {

    // API CRUD de Stock
    Route::get('stock', [ApiStockController::class, 'index']); 
    
    // Ruta de actualización de stock (PUT /api/stock/{product})
    // Notación 'stock/{product}' es correcta para resource binding
    Route::put('stock/{product}', [ApiStockController::class, 'updateStock']); 

    // API de Entrada Rápida por QR
    // Ruta de QR 'stock/receive-qr' es correcta (se convierte en /api/stock/receive-qr)
    Route::post('stock/receive-qr', [ApiStockController::class, 'receiveStockByQR']);
});