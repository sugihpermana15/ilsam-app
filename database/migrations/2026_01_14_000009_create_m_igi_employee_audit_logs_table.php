<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('m_igi_employee_audit_logs', function (Blueprint $table) {
      $table->id();

      $table->foreignId('employee_id')
        ->nullable()
        ->constrained('m_igi_employees')
        ->nullOnDelete();

      $table->string('action', 20);

      $table->foreignId('performed_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

      $table->string('performed_by_name')->nullable();
      $table->string('ip_address', 45)->nullable();
      $table->text('user_agent')->nullable();

      $table->json('old_values')->nullable();
      $table->json('new_values')->nullable();

      $table->timestamp('created_at')->useCurrent();

      $table->index(['employee_id', 'action']);
      $table->index(['performed_by', 'created_at']);
      $table->index('created_at');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_employee_audit_logs');
  }
};
