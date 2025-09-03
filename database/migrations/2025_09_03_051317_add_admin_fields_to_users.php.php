<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','role')) {
                $table->string('role')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users','is_admin')) {
                $table->boolean('is_admin')->default(false)->after('role');
            }
            if (!Schema::hasColumn('users','is_approved')) {
                $table->boolean('is_approved')->default(false)->after('is_admin');
            }
            if (!Schema::hasColumn('users','legajo')) {
                $table->string('legajo', 50)->nullable()->after('is_approved');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','legajo')) $table->dropColumn('legajo');
            if (Schema::hasColumn('users','is_approved')) $table->dropColumn('is_approved');
            if (Schema::hasColumn('users','is_admin')) $table->dropColumn('is_admin');
            if (Schema::hasColumn('users','role')) $table->dropColumn('role');
        });
    }
};
