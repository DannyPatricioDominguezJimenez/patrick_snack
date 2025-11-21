<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Importaciones necesarias
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\SaleController;
// Nota: Las importaciones automáticas (::class) se usan para mayor claridad

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rutas Públicas
Route::get('/', function () {
    return view('welcome');
});

// 🚨 CORRECCIÓN 1: Dejamos solo la ruta del Dashboard que usa el controlador
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// RUTAS PROTEGIDAS (Requieren Autenticación de Sesión Web)
Route::middleware('auth')->group(function () {
    
    // --- MÓDULO DE PERFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MÓDULO DE CLIENTES Y CATEGORÍAS ---
    Route::resource('clientes', ClientController::class);
    Route::resource('categories', ClientCategoryController::class)->except(['index', 'create', 'show', 'edit']);

    // --- MÓDULO DE PRODUCTOS Y CATEGORÍAS ---
    Route::resource('productos', ProductController::class);
    Route::resource('product_categories', ProductCategoryController::class)->except(['index', 'create', 'show', 'edit']);
    
    // --- MÓDULO DE STOCK (El get/put deben estar juntos) ---
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::put('/stock/{producto}', [StockController::class, 'update'])->name('stock.update');
    
    // --- MÓDULO DE CALENDARIO/ACTIVIDADES ---
    Route::get('/calendario', [DailyLogController::class, 'index'])->name('calendario.index');
    Route::post('/diariolog', [DailyLogController::class, 'store'])->name('diariolog.store');
    Route::put('/diariolog/{dailyLog}', [DailyLogController::class, 'update'])->name('diariolog.update');
    Route::delete('/diariolog/{dailyLog}', [DailyLogController::class, 'destroy'])->name('diariolog.destroy');

    // --- MÓDULO DE VENTAS ---
    Route::resource('ventas', SaleController::class);
    // 🚨 CORRECCIÓN 2: Eliminamos la ruta duplicada 'ventas/{sale}/invoice'
    Route::get('ventas/{sale}/invoice', [SaleController::class, 'generateInvoice'])->name('ventas.invoice'); 
    Route::get('ventas/{sale}/download', [SaleController::class, 'downloadInvoice'])->name('ventas.download');
});


require __DIR__.'/auth.php'; // Incluye las rutas de autenticación web (GET /login, POST /login, etc.)