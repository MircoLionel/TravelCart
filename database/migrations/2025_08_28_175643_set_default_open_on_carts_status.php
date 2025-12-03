<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        DB::table('carts')
            ->whereNull('status')
            ->orWhere('status', 'pending')
            ->update(['status' => 'open']);

        if ($driver === 'sqlite') {
            // SQLite cannot easily alter constrained columns, but open/closed values
            // are allowed because enums are stored as TEXT. Bail out gracefully.
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE carts ALTER COLUMN status DROP DEFAULT");
            DB::statement("ALTER TABLE carts ALTER COLUMN status TYPE VARCHAR(20) USING status::text");
            DB::statement("ALTER TABLE carts ALTER COLUMN status SET DEFAULT 'open'");
            DB::statement("ALTER TABLE carts ALTER COLUMN status SET NOT NULL");

            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE carts MODIFY status VARCHAR(20) NOT NULL DEFAULT 'open'");

            return;
        }

        Schema::table('carts', function (Blueprint $table) {
            $table->string('status', 20)->default('open')->change();
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::table('carts')
                ->where('status', 'open')
                ->update(['status' => null]);

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE carts ALTER COLUMN status DROP DEFAULT");
            DB::statement("ALTER TABLE carts ALTER COLUMN status DROP NOT NULL");

            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE carts MODIFY status VARCHAR(20) NULL DEFAULT NULL");

            return;
        }

        Schema::table('carts', function (Blueprint $table) {
            $table->string('status', 20)->nullable()->default(null)->change();
        });
    }
};
