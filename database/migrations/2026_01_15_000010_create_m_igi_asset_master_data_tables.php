<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('m_igi_asset_categories', function (Blueprint $table) {
      $table->id();
      $table->string('code', 50)->unique();
      $table->string('name', 100);
      $table->string('asset_code_prefix', 10);
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });

    Schema::create('m_igi_asset_locations', function (Blueprint $table) {
      $table->id();
      $table->string('name', 100)->unique();
      $table->string('asset_code_prefix', 5);
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });

    Schema::create('m_igi_asset_uoms', function (Blueprint $table) {
      $table->id();
      $table->string('name', 50)->unique();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });

    Schema::create('m_igi_asset_vendors', function (Blueprint $table) {
      $table->id();
      $table->string('name', 100)->unique();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_asset_vendors');
    Schema::dropIfExists('m_igi_asset_uoms');
    Schema::dropIfExists('m_igi_asset_locations');
    Schema::dropIfExists('m_igi_asset_categories');
  }
};
