<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UsuarioController extends Controller
{
    /**
     * Lista paginada de usuarios con búsqueda y cantidad dinámica por página.
     */
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $porPagina = $request->get('per_page', 5);

        // Limitar las opciones válidas de paginación
        $porPagina = in_array((int) $porPagina, [5, 10, 25]) ? (int) $porPagina : 5;

        $usuarios = User::where('name', 'LIKE', "%$buscar%")
                        ->orWhere('email', 'LIKE', "%$buscar%")
                        ->orderBy('created_at', 'desc')
                        ->paginate($porPagina)
                        ->withQueryString();

        $totalUsuarios = User::count();

        return view('usuarios', compact('usuarios', 'totalUsuarios'));
    }

    /**
     * Crea un nuevo usuario con validación completa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|min:3|max:50',
            'correo' => 'required|email|unique:users,email',
        ], [
            'nombre.required' => 'Oye, el nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 letras.',
            'correo.required' => 'Necesitamos un email para contactarte.',
            'correo.email' => 'Ese formato de correo no es válido.',
            'correo.unique' => 'Ese correo ya está registrado en la base de datos.',
        ]);

        User::create([
            'name' => $request->nombre,
            'email' => $request->correo,
            'password' => bcrypt('123456')
        ]);

        return back()->with('success', '¡Usuario creado con éxito!');
    }

    /**
     * Elimina un usuario por su ID.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $nombreUsuario = $user->name;
        $user->delete();

        return back()->with('success', "Usuario \"$nombreUsuario\" eliminado correctamente.");
    }

    /**
     * Actualiza un usuario con validación.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nombre' => 'required|min:3|max:50',
            'correo' => 'required|email|unique:users,email,' . $user->id,
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 letras.',
            'correo.required' => 'Necesitamos un email.',
            'correo.email' => 'Formato de correo no válido.',
            'correo.unique' => 'Ese correo ya pertenece a otro usuario.',
        ]);

        $user->update([
            'name' => $request->nombre,
            'email' => $request->correo,
        ]);

        return back()->with('success', "Usuario \"$user->name\" actualizado correctamente.");
    }

    /**
     * Exporta todos los usuarios a un archivo CSV descargable.
     */
    public function export(): StreamedResponse
    {
        $filename = 'usuarios_' . date('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // Cabecera CSV
            fputcsv($handle, ['ID', 'Nombre', 'Email', 'Fecha de Registro']);

            // Datos
            User::orderBy('id')
                ->chunk(100, function ($users) use ($handle) {
                    foreach ($users as $user) {
                        fputcsv($handle, [
                            $user->id,
                            $user->name,
                            $user->email,
                            $user->created_at->format('d/m/Y H:i'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Devuelve estadísticas para el dashboard.
     */
    public function stats(): array
    {
        $totalUsuarios = User::count();
        $ultimoUsuario = User::latest()->first();
        $usuariosHoy = User::whereDate('created_at', today())->count();

        return compact('totalUsuarios', 'ultimoUsuario', 'usuariosHoy');
    }
}
