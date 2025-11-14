<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/clientes', [\App\Http\Controllers\ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/productos', [\App\Http\Controllers\ProductoController::class, 'index'])->name('productos.index');
    Route::get('/stock', [\App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
    Route::get('/calendario', [\App\Http\Controllers\CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/ventas', [\App\Http\Controllers\VentaController::class, 'index'])->name('ventas.index');
    Route::resource('clientes', App\Http\Controllers\ClientController::class)->middleware(['auth']);
    Route::resource('categories', App\Http\Controllers\ClientCategoryController::class)->except(['index', 'create', 'show', 'edit']);
    Route::resource('product_categories', App\Http\Controllers\ProductCategoryController::class)->except(['index', 'create', 'show', 'edit']);
    Route::resource('productos', App\Http\Controllers\ProductController::class);
    Route::get('/stock', [App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
    Route::put('/stock/{producto}', [App\Http\Controllers\StockController::class, 'update'])->name('stock.update');
    Route::get('/calendario', [App\Http\Controllers\DailyLogController::class, 'index'])->name('calendario.index');


    Route::post('/diariolog', [App\Http\Controllers\DailyLogController::class, 'store'])->name('diariolog.store');
Route::put('/diariolog/{dailyLog}', [App\Http\Controllers\DailyLogController::class, 'update'])->name('diariolog.update'); // Para editar
Route::delete('/diariolog/{dailyLog}', [App\Http\Controllers\DailyLogController::class, 'destroy'])->name('diariolog.destroy'); // Para eliminar

Route::resource('ventas', App\Http\Controllers\SaleController::class);
Route::get('ventas/{sale}/invoice', [App\Http\Controllers\SaleController::class, 'generateInvoice'])->name('ventas.invoice');
Route::get('ventas/{sale}/invoice', [App\Http\Controllers\SaleController::class, 'generateInvoice'])->name('ventas.invoice'); // <- VISTA PREVIA
Route::get('ventas/{sale}/download', [App\Http\Controllers\SaleController::class, 'downloadInvoice'])->name('ventas.download'); // <- DESCARGA DIRECTA
});




require __DIR__.'/auth.php';
