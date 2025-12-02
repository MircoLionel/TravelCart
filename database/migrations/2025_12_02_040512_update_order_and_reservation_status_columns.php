<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Permitir nuevos estados (ej. awaiting_passengers, pending_payment) sin restricciones de ENUM
            $table->string('status', 50)->default('awaiting_passengers')->change();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('status', 50)->default('awaiting_passengers')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 20)->default('paid')->change();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('status', 20)->default('confirmed')->change();
        });
    }
};
