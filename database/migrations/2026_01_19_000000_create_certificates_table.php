<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('certificates', function (Blueprint $table) {
      $table->id();
      $table->string('chemical_name', 200);
      $table->string('supplier', 200)->nullable();
      $table->string('certification_type', 200)->nullable();
      $table->string('certificate_no', 120)->nullable();
      $table->date('issued_date')->nullable();
      $table->date('expiry_date')->nullable();
      $table->string('scope', 255)->nullable();
      $table->string('zdhc_link', 500)->nullable();
      $table->string('proof_path', 500)->nullable();
      $table->timestamps();

      $table->index(['chemical_name']);
      $table->index(['supplier']);
      $table->index(['expiry_date']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('certificates');
  }
};
