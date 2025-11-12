<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::table('carts')
                ->whereNull('status')
                ->orWhere('status', 'pending')
                ->update(['status' => 'open']);

            return;
        }

        DB::table('carts')->whereNull('status')->update(['status' => 'open']);
        DB::statement("ALTER TABLE carts MODIFY status VARCHAR(20) NOT NULL DEFAULT 'open'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::table('carts')
                ->where('status', 'open')
                ->update(['status' => null]);

            return;
        }

        DB::statement("ALTER TABLE carts MODIFY status VARCHAR(20) NULL DEFAULT NULL");
    }
};
