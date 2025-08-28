<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalizo datos existentes
        DB::table('carts')->whereNull('status')->update(['status' => 'open']);

        // MySQL/MariaDB: ajustá si tu tipo difiere
        DB::statement("ALTER TABLE carts MODIFY status VARCHAR(20) NOT NULL DEFAULT 'open'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE carts MODIFY status VARCHAR(20) NULL DEFAULT NULL");
    }
};
