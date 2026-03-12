<?php

use App\Http\Controllers\Admin\CarAdminController;
use App\Http\Controllers\Admin\McpTokenController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard (Welcome)
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Token MCP: disponible para cualquier usuario autenticado.
    Route::get('/admin/mcp-token', [McpTokenController::class, 'show'])->name('admin.mcp-token.show');
    Route::post('/admin/mcp-token', [McpTokenController::class, 'store'])->name('admin.mcp-token.store');
});

Route::prefix('tienda')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/coches/{car:slug}', [ShopController::class, 'show'])->name('show');
    Route::get('/coches/{car:slug}/ir', [ShopController::class, 'outbound'])->name('outbound');
});

Route::middleware(['auth', 'can:manage-users'])->group(function () {
    Route::prefix('admin/tienda')->name('admin.shop.')->group(function () {
        Route::get('/coches', [CarAdminController::class, 'index'])->name('cars.index');
        Route::post('/coches', [CarAdminController::class, 'store'])->name('cars.store');
        Route::put('/coches/{car:id}', [CarAdminController::class, 'update'])->name('cars.update');
        Route::delete('/coches/{car:id}', [CarAdminController::class, 'destroy'])->name('cars.destroy');

        Route::get('/pedidos', [OrderAdminController::class, 'index'])->name('orders.index');
        Route::patch('/pedidos/{order:id}', [OrderAdminController::class, 'update'])->name('orders.update');
    });

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

    /*
    |--------------------------------------------------------------------------
    | Exportar Usuarios a CSV (debe ir ANTES de la ruta con {user})
    |--------------------------------------------------------------------------
    */
    Route::get('/usuarios/exportar', [UsuarioController::class, 'export'])->name('usuarios.export');

    Route::put('/usuarios/{user}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{user}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
});
