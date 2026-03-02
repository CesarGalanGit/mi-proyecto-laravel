<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\UsuarioController;

Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);