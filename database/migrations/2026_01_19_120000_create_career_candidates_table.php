<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('career_candidates', function (Blueprint $table) {
      $table->id();

      $table->string('job_id', 36)->nullable();
      $table->string('job_title', 160)->nullable();

      $table->string('full_name', 160);
      $table->string('email', 200);
      $table->string('phone', 60);
      $table->string('domicile', 160)->nullable();
      $table->string('linkedin_url', 500)->nullable();
      $table->string('portfolio_url', 500)->nullable();
      $table->text('message')->nullable();

      $table->string('cv_path', 500);
      $table->string('cv_original_name', 255);
      $table->string('cv_mime', 120)->nullable();
      $table->unsignedBigInteger('cv_size')->nullable();

      $table->string('ip_address', 45)->nullable();
      $table->text('user_agent')->nullable();

      $table->timestamps();

      $table->index(['job_id']);
      $table->index(['email']);
      $table->index(['created_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('career_candidates');
  }
};
