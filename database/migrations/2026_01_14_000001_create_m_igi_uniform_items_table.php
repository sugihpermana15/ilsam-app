<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('m_igi_uniform_items', function (Blueprint $table) {
      $table->id();
      $table->string('item_code')->unique();
      $table->string('item_name');
      $table->string('category');
      $table->string('size')->nullable();
      $table->string('color')->nullable();
      $table->string('uom', 20)->default('pcs');
      $table->string('location')->default('Jababeka');
      $table->unsignedInteger('min_stock')->nullable();
      $table->unsignedInteger('current_stock')->default(0);
      $table->boolean('is_active')->default(true);
      $table->timestamp('input_date')->useCurrent();
      $table->timestamp('last_updated')->nullable();
      $table->text('notes')->nullable();

      $table->index(['location', 'is_active']);
      $table->index(['category']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_uniform_items');
  }
};
