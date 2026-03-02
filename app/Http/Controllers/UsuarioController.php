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
    // 1. Reglas de validación
    $request->validate([
        'nombre' => 'required|min:3|max:50',
        'correo' => 'required|email|unique:users,email',
    ], [
        // Mensajes personalizados en español
        'nombre.required' => 'Oye, el nombre es obligatorio.',
        'nombre.min' => 'El nombre debe tener al menos 3 letras.',
        'correo.required' => 'Necesitamos un email para contactarte.',
        'correo.email' => 'Ese formato de correo no es válido.',
        'correo.unique' => 'Ese correo ya está registrado en la base de datos.',
    ]);

    // 2. Si pasa la validación, se guarda
    User::create([
        'name' => $request->nombre,
        'email' => $request->correo,
        'password' => bcrypt('123456')
    ]);

    return back()->with('success', '¡Usuario guardado con éxito!');
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
