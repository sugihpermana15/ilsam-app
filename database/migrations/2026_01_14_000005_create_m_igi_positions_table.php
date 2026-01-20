<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('m_igi_positions', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique();
      $table->string('level_code', 10);
      $table->timestamps();

      $table->index('level_code');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_positions');
  }
};
