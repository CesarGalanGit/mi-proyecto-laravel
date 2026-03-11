<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use PDOException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('mcp', function (Request $request) {
            $perMinute = (int) config('mcp.rate_limit_per_minute', 60);

            $userId = (string) ($request->user('sanctum')?->getAuthIdentifier() ?? '');
            $key = $userId !== ''
                ? 'mcp|user:'.$userId
                : 'mcp|ip:'.$request->ip();

            return Limit::perMinute(max(1, $perMinute))
                ->by($key);
        });

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Gate::define('manage-users', function (User $user): bool {
            return $user->email === config('admin.email');
        });

        // Compartir el total de usuarios con el layout para el badge del sidebar
        View::composer('layouts.app', function ($view) {
            try {
                $totalUsuarios = Cache::remember('users.count', now()->addSeconds(30), function (): int {
                    return User::count();
                });

                $view->with('totalUsuarios', $totalUsuarios);
            } catch (QueryException|PDOException) {
                // Si la BD no está disponible (ej: migraciones pendientes), no romper
                $view->with('totalUsuarios', 0);
            }
        });
    }
}
