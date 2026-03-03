<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UsuarioController extends Controller
{
    /**
     * Lista paginada de usuarios con búsqueda y cantidad dinámica por página.
     */
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $porPagina = (int) $request->query('per_page', 5);

        // Limitar las opciones válidas de paginación
        $porPagina = in_array($porPagina, [5, 10, 25], true) ? $porPagina : 5;

        $usuarios = User::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query
                        ->where('name', 'LIKE', "%{$buscar}%")
                        ->orWhere('email', 'LIKE', "%{$buscar}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($porPagina)
            ->withQueryString();

        $totalUsuarios = User::count();

        return view('usuarios', compact('usuarios', 'totalUsuarios'));
    }

    /**
     * Crea un nuevo usuario con validación completa.
     */
    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['nombre'],
            'email' => $validated['correo'],
            'password' => $validated['password'],
        ]);

        return back()->with('success', '¡Usuario creado con éxito!');
    }

    /**
     * Elimina un usuario por su ID.
     */
    public function destroy(User $user): RedirectResponse
    {
        $nombreUsuario = $user->name;
        $user->delete();

        return back()->with('success', "Usuario \"$nombreUsuario\" eliminado correctamente.");
    }

    /**
     * Actualiza un usuario con validación.
     */
    public function update(UpdateUsuarioRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['nombre'],
            'email' => $validated['correo'],
        ];

        if (! empty($validated['password'] ?? null)) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return back()->with('success', "Usuario \"$user->name\" actualizado correctamente.");
    }

    /**
     * Exporta todos los usuarios a un archivo CSV descargable.
     */
    public function export(): StreamedResponse
    {
        $filename = 'usuarios_'.date('Y-m-d_H-i').'.csv';

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
}
