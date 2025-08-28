<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('tours', function (Blueprint $t) {
      $t->id();
      $t->string('title');
      $t->text('description')->nullable();
      $t->decimal('base_price', 12, 2);
      $t->unsignedSmallInteger('days')->default(1);
      $t->string('origin')->nullable();
      $t->string('destination');
      $t->boolean('is_active')->default(true);
      $t->timestamps();
    });
  }
  public function down(): void {
    Schema::dropIfExists('tours');
  }
};