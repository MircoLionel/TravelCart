<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->softDeletes(); // agrega columna deleted_at
        });
        Schema::table('tour_dates', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Opcional (si querés papelera también en carrito):
        // Schema::table('carts', fn (Blueprint $t) => $t->softDeletes());
        // Schema::table('cart_items', fn (Blueprint $t) => $t->softDeletes());
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('tour_dates', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // Opcional, si los agregaste:
        // Schema::table('carts', fn (Blueprint $t) => $t->dropSoftDeletes());
        // Schema::table('cart_items', fn (Blueprint $t) => $t->dropSoftDeletes());
    }
};
