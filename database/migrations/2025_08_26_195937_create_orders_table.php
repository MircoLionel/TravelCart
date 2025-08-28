<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('orders', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->string('code')->unique(); // código legible
      $t->enum('status', ['pending','paid','cancelled'])->default('paid'); // simulamos pago OK
      $t->decimal('total', 12, 2);
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('orders'); }
};