<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('m_igi_uniform_item_names', function (Blueprint $table) {
      $table->id();
      $table->string('name', 255)->unique();
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index(['is_active', 'name']);
    });

    Schema::create('m_igi_uniform_categories', function (Blueprint $table) {
      $table->id();
      $table->string('name', 100)->unique();
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index(['is_active', 'name']);
    });

    Schema::create('m_igi_uniform_colors', function (Blueprint $table) {
      $table->id();
      $table->string('name', 50)->unique();
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index(['is_active', 'name']);
    });

    Schema::create('m_igi_uniform_uoms', function (Blueprint $table) {
      $table->id();
      $table->string('code', 20)->unique();
      $table->string('name', 50)->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index(['is_active', 'code']);
    });

    // Seed defaults (idempotent)
    DB::table('m_igi_uniform_uoms')->updateOrInsert(
      ['code' => 'pcs'],
      ['name' => 'pcs', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
    );

    DB::table('m_igi_uniform_categories')->updateOrInsert(
      ['name' => 'Uniform'],
      ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
    );
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_uniform_uoms');
    Schema::dropIfExists('m_igi_uniform_colors');
    Schema::dropIfExists('m_igi_uniform_categories');
    Schema::dropIfExists('m_igi_uniform_item_names');
  }
};
