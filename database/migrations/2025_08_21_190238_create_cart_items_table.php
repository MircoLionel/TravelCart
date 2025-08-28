<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('cart_items', function (Blueprint $t) {
      $t->id();
      $t->foreignId('cart_id')->constrained()->cascadeOnDelete();
      $t->foreignId('tour_id')->constrained()->cascadeOnDelete();
      $t->foreignId('tour_date_id')->constrained()->cascadeOnDelete();
      $t->unsignedInteger('qty');
      $t->decimal('unit_price', 12, 2);
      $t->decimal('subtotal', 12, 2);
      $t->timestamps();

      $t->unique(['cart_id','tour_id','tour_date_id']); // 1 línea por tour/fecha
    });
  }
  public function down(): void { Schema::dropIfExists('cart_items'); }
};