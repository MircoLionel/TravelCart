<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('tour_dates', function (Blueprint $t) {
      $t->id();
      $t->foreignId('tour_id')->constrained()->cascadeOnDelete();
      $t->date('start_date');
      $t->date('end_date');
      $t->unsignedInteger('capacity');
      $t->unsignedInteger('available');
      $t->decimal('price', 12, 2);
      $t->boolean('is_active')->default(true);
      $t->timestamps();
    });
  }
  public function down(): void {
    Schema::dropIfExists('tour_dates');
  }
};
