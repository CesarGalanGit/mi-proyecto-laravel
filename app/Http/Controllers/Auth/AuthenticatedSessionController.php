<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $default = ($user !== null && $user->can('manage-users'))
            ? route('usuarios.index')
            : route('dashboard');

        $intended = redirect()->getIntendedUrl();
        if ($intended !== null && ($user === null || ! $user->can('manage-users'))) {
            $path = (string) parse_url($intended, PHP_URL_PATH);
            if (
                str_starts_with($path, '/usuarios')
                || (str_starts_with($path, '/admin') && ! str_starts_with($path, '/admin/mcp-token'))
            ) {
                $request->session()->forget('url.intended');
            }
        }

        return redirect()->intended($default);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dashboard');
    }
}
