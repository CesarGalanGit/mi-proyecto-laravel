<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\View\View;
use PDOException;

class DashboardController extends Controller
{
    public function index(): View
    {
        try {
            $totalUsuarios = User::count();
            $ultimoUsuario = User::latest()->first();
            $usuariosHoy = User::whereDate('created_at', today())->count();
        } catch (QueryException|PDOException) {
            $totalUsuarios = 0;
            $ultimoUsuario = null;
            $usuariosHoy = 0;
        }

        return view('welcome', compact('totalUsuarios', 'ultimoUsuario', 'usuariosHoy'));
    }
}
