<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Throwable;

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

        $databaseJustCreated = false;

        if (! File::exists($databasePath)) {
            File::ensureDirectoryExists(dirname($databasePath));
            File::put($databasePath, '');
            $databaseJustCreated = true;
        }

        if (! $databaseJustCreated
            && Schema::hasTable('migrations')
            && Schema::hasTable('tours')
        ) {
            return;
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
