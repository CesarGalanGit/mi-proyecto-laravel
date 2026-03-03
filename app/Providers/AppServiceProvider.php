<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

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
        // Compartir el total de usuarios con el layout para el badge del sidebar
        View::composer('layouts.app', function ($view) {
            try {
                $view->with('totalUsuarios', User::count());
            } catch (\Throwable) {
                // Si la BD no está disponible (ej: migraciones pendientes), no romper
                $view->with('totalUsuarios', 0);
            }
        });
    }
}
