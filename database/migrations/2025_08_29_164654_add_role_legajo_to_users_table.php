<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rol del usuario: customer | vendor | admin
            $table->string('role')->default('customer')->after('password');
            // Aprobación para operar (carrito/checkout)
            $table->boolean('is_approved')->default(false)->after('role');
            // Nro. de legajo (único, obligatorio para operar)
            $table->string('legajo')->nullable()->unique()->after('is_approved');

            // (Opcional) Índice para búsquedas por rol
            $table->index('role');
        });

        // Backfill: si existe columna legacy is_admin, marcamos esos usuarios como admin y aprobados.
        if (Schema::hasColumn('users', 'is_admin')) {
            DB::table('users')->where('is_admin', 1)->update([
                'role'        => 'admin',
                'is_approved' => 1,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropear columnas; al dropear 'legajo' se elimina también su índice único
            if (Schema::hasColumn('users', 'legajo')) {
                $table->dropColumn('legajo');
            }
            if (Schema::hasColumn('users', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
            if (Schema::hasColumn('users', 'role')) {
                // dropea también el index creado arriba si existe
                try { $table->dropIndex(['role']); } catch (\Throwable $e) {}
                $table->dropColumn('role');
            }
        });
    }
};
