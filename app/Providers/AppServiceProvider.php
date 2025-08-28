<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Aquí puedes registrar bindings o singletons si los necesitas.
    }

    public function boot(): void
    {
        // Gate para proteger /admin
        // Usamos el FQCN para evitar confusiones de namespace.
        Gate::define('admin', function (\App\Models\User $user): bool {
            return (bool) $user->is_admin;
        });
    }
}
