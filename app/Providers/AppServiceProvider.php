<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Router $router): void
    {
        // Fuerza el alias 'approved' por si el Kernel no se resolvió antes
        $router->aliasMiddleware('approved', \App\Http\Middleware\EnsureApproved::class);
    }
}
