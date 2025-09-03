<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * A dónde redirigir luego de login (usado por Auth).
     */
    public const HOME = '/dashboard';

    /**
     * Define las rutas de la aplicación.
     */
    public function boot(): void
    {
        $this->routes(function () {
            // API (si existe el archivo)
            if (file_exists(base_path('routes/api.php'))) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('routes/api.php'));
            }

            // Web (debe existir)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
