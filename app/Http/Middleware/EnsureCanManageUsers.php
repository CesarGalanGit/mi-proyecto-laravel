<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanManageUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->can('manage-users')) {
            return $next($request);
        }

        // Para APIs / peticiones JSON, mantenemos el comportamiento estandar.
        if ($request->expectsJson()) {
            abort(403);
        }

        // Para navegacion web (especialmente GET), mostramos una vista mas amigable.
        if ($request->isMethod('get') || $request->isMethod('head')) {
            return response()->view('usuarios.forbidden', status: 403);
        }

        abort(403);
    }
}
