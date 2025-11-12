<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percent','fixed']); // percent = % | fixed = monto
            $table->unsignedInteger('amount');         // percent: 1..100 | fixed: en pesos (enteros)
            $table->unsignedInteger('min_total')->default(0); // mínimo subtotal para aplicar
            $table->enum('role_only', ['admin','vendor','buyer'])->nullable(); // restringir a un rol
            $table->unsignedInteger('max_uses')->nullable();     // total de usos global
            $table->unsignedInteger('max_uses_per_user')->nullable(); // por usuario
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('discount'); // en pesos
            $table->timestamps();

            $table->index(['coupon_id', 'user_id']);
        });

        // Cols en orders para registrar lo aplicado
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'discount_total')) {
                $table->unsignedInteger('discount_total')->default(0);
            }
            if (!Schema::hasColumn('orders', 'applied_coupon_code')) {
                $table->string('applied_coupon_code')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'discount_total')) $table->dropColumn('discount_total');
            if (Schema::hasColumn('orders', 'applied_coupon_code')) $table->dropColumn('applied_coupon_code');
        });
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('coupons');
    }
};
