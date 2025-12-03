<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Throwable;

class TravelCartUpgradeCommand extends Command
{
    protected $signature = 'travelcart:upgrade {--seed : Ejecuta los seeders luego de migrar}';

    protected $description = 'Ejecuta las migraciones pendientes y (opcionalmente) los seeders para actualizar el esquema de TravelCart.';

    public function handle(): int
    {
        $connection = Config::get('database.default');
        $database = Config::get("database.connections.{$connection}.database");

        $this->info("Base de datos: {$connection} ({$database})");

        try {
            $this->info('Ejecutando migraciones pendientes...');
            Artisan::call('migrate', ['--force' => true]);
            $this->output->write(Artisan::output());

            if ($this->option('seed')) {
                $this->info('Ejecutando seeders...');
                Artisan::call('db:seed', ['--force' => true]);
                $this->output->write(Artisan::output());
            }

            $missingTables = collect([
                'tours',
                'reservations',
                'vendor_buyer_links',
                'reservation_passengers',
                'reservation_payments',
            ])->filter(fn ($table) => ! Schema::connection($connection)->hasTable($table));

            if ($missingTables->isNotEmpty()) {
                $this->warn('Aún faltan tablas clave: '.implode(', ', $missingTables->all()));
                $this->warn('Revisá tus credenciales en .env y volvé a ejecutar el comando.');
            } else {
                $this->info('Esquema actualizado correctamente.');
            }

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('No se pudieron aplicar las migraciones.');
            $this->error($exception->getMessage());

            $this->line('Tip: verificá el archivo .env (sin modificarlo desde este comando) y que el usuario tenga permisos de ALTER/CREATE.');
            return Command::FAILURE;
        }
    }
}
