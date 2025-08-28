<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('order_items', function (Blueprint $t) {
      $t->id();
      $t->foreignId('order_id')->constrained()->cascadeOnDelete();
      $t->foreignId('tour_id')->constrained()->cascadeOnDelete();
      $t->foreignId('tour_date_id')->constrained()->cascadeOnDelete();
      $t->unsignedInteger('qty');
      $t->decimal('unit_price', 12, 2);
      $t->decimal('subtotal', 12, 2);
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('order_items'); }
};
