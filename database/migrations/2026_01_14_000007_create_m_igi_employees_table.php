<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('m_igi_employees', function (Blueprint $table) {
      $table->id();

      $table->unsignedInteger('sequence_number')->unique();
      $table->string('no_id', 30)->unique();

      $table->string('name');
      $table->enum('gender', ['Laki-laki', 'Perempuan']);
      $table->date('birth_date');
      $table->text('address');
      $table->string('phone', 30);
      $table->string('email')->unique();

      $table->foreignId('department_id')
        ->constrained('m_igi_departments')
        ->restrictOnDelete();

      $table->foreignId('position_id')
        ->constrained('m_igi_positions')
        ->restrictOnDelete();

      $table->date('join_date');
      $table->string('photo')->nullable();

      $table->timestamps();

      $table->index(['department_id', 'position_id']);
      $table->index('join_date');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_employees');
  }
};
