<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('carts', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->enum('status', ['pending','converted'])->default('pending');
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('carts'); }
};
