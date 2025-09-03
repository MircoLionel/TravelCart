<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! Gate::has('admin')) {
            Gate::define('admin', function (User $user) {
                return ($user->role === 'admin') || (bool) ($user->is_admin ?? false);
            });
        }
    }
}
