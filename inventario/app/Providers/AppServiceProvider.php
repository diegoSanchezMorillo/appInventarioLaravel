<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
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
        // Los enlaces de paginación usan las clases de Bootstrap 5
        // (por defecto Laravel los dibuja con Tailwind y se verían mal)
        Paginator::useBootstrapFive();

        // Permiso "admin": solo lo cumplen los usuarios con es_admin = true.
        // Se usa en las rutas con middleware('can:admin') y en las vistas con @can('admin')
        Gate::define('admin', fn (User $user) => $user->es_admin);
    }
}
