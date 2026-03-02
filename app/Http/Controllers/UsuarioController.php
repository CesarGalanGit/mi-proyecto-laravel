<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index() {
        $usuarios = User::all(); // Trae todos los de phpMyAdmin
        return view('usuarios', compact('usuarios'));
    }

    public function store(Request $request) {
        User::create([
            'name' => $request->nombre,
            'email' => $request->correo,
            'password' => bcrypt('123456') // Password por defecto
        ]);
        return back(); // Recarga la página para ver el nuevo nombre
    }

    public function destroy($id) {
        User::destroy($id); // Borra al usuario por su ID
        return back();
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->nombre,
            'email' => $request->correo
        ]);
        return back();
    }
 
}
