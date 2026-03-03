<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| Dashboard (Welcome)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    try {
        $controller = new UsuarioController();
        $stats = $controller->stats();
    } catch (\Throwable) {
        // Si la BD no está disponible, mostrar valores por defecto
        $stats = ['totalUsuarios' => 0, 'ultimoUsuario' => null, 'usuariosHoy' => 0];
    }

    return view('welcome', $stats);
});

/*
|--------------------------------------------------------------------------
| Exportar Usuarios a CSV (debe ir ANTES de la ruta con {id})
|--------------------------------------------------------------------------
*/
Route::get('/usuarios/exportar', [UsuarioController::class, 'export']);

/*
|--------------------------------------------------------------------------
| CRUD de Usuarios
|--------------------------------------------------------------------------
*/
Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);