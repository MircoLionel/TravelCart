<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete(); // quién realizó
            $table->string('action');            // user_updated, order_status_changed, etc
            $table->string('target_type');       // App\Models\User, App\Models\Order, etc
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('meta')->nullable();    // cambios, campos, valores
            $table->timestamps();

            $table->index(['target_type','target_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('audits');
    }
};
