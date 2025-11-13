<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (Config::get('database.default') !== 'sqlite') {
            return;
        }

        $databasePath = Config::get('database.connections.sqlite.database');

        if (! $databasePath || Str::startsWith($databasePath, [':memory:', 'file:'])) {
            return;
        }

        if (! Str::startsWith($databasePath, [DIRECTORY_SEPARATOR, '\\'])
            && ! preg_match('/^[A-Za-z]:\\\\/', $databasePath)
        ) {
            $databasePath = base_path($databasePath);
        }

        if (File::exists($databasePath)) {
            return;
        }

        File::ensureDirectoryExists(dirname($databasePath));
        File::put($databasePath, '');
    }
}
