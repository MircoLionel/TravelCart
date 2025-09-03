<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Model => Policy (si usas policies, van aquí)
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define "admin" SOLO si aún no existe (evita colisiones si se define en otro provider)
        if (! Gate::has('admin')) {
            Gate::define('admin', function (User $user) {
                // Soporta ambos esquemas: role=admin o is_admin=1
                return ($user->role === 'admin') || (bool) ($user->is_admin ?? false);
            });
        }

        // (opcional) vendor
        if (! Gate::has('vendor')) {
            Gate::define('vendor', fn (User $user) => $user->role === 'vendor');
        }
    }
}
