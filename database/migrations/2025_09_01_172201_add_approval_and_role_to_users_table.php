<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'legajo')) {
                $table->string('legajo')->nullable()->after('is_approved');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('buyer')->after('legajo'); // buyer | vendor | admin
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'legajo')) {
                $table->dropColumn('legajo');
            }
            if (Schema::hasColumn('users', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
        });
    }
};
