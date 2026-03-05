<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
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
