<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reservations', function (Blueprint $t) {
      $t->id();
      $t->foreignId('order_id')->constrained()->cascadeOnDelete();
      $t->foreignId('tour_id')->constrained()->cascadeOnDelete();
      $t->foreignId('tour_date_id')->constrained()->cascadeOnDelete();
      $t->unsignedInteger('qty');
      $t->enum('status', ['pending','confirmed','cancelled'])->default('confirmed');
      $t->string('locator')->unique();
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('reservations'); }
};