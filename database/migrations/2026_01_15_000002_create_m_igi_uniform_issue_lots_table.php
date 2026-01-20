<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('m_igi_uniform_issue_lots', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('issue_id');
      $table->unsignedBigInteger('lot_id');
      $table->unsignedInteger('qty');
      $table->timestamps();

      $table->foreign('issue_id')->references('id')->on('m_igi_uniform_issues');
      $table->foreign('lot_id')->references('id')->on('m_igi_uniform_lots');

      $table->unique(['issue_id', 'lot_id']);
      $table->index(['lot_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_uniform_issue_lots');
  }
};
