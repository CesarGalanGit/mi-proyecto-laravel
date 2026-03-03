<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
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
});

Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

Route::middleware(['auth', 'can:manage-users'])->group(function () {
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
