<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // \App\Models\Model::class => \App\Policies\ModelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Usuario administrador (usa la columna boolean is_admin)
        Gate::define('admin', function (User $user): bool {
            return (bool) $user->is_admin;
        });

        // Usuario aprobado para operar (usa la columna boolean is_approved)
        Gate::define('approved', function (User $user): bool {
            return (bool) $user->is_approved;
        });
    }
}
