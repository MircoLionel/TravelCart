<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (!Schema::hasColumn('tours', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('order_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('reservations', 'total_amount')) {
                $table->unsignedBigInteger('total_amount')->default(0)->after('qty');
            }
            if (!Schema::hasColumn('reservations', 'hold_expires_at')) {
                $table->timestamp('hold_expires_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'hold_expires_at')) {
                $table->dropColumn('hold_expires_at');
            }
            if (Schema::hasColumn('reservations', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
            if (Schema::hasColumn('reservations', 'vendor_id')) {
                $table->dropConstrainedForeignId('vendor_id');
            }
        });

        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'vendor_id')) {
                $table->dropConstrainedForeignId('vendor_id');
            }
        });
    }
};
