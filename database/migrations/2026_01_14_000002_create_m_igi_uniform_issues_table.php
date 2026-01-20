<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('m_igi_uniform_issues', function (Blueprint $table) {
      $table->id();
      $table->string('issue_code')->unique();
      $table->unsignedBigInteger('uniform_item_id');
      $table->unsignedBigInteger('issued_to_user_id');
      $table->unsignedInteger('qty');
      $table->enum('status', ['ISSUED', 'RETURNED', 'LOST', 'DAMAGED'])->default('ISSUED');
      $table->text('notes')->nullable();
      $table->unsignedBigInteger('issued_by')->nullable();
      $table->timestamp('issued_at')->useCurrent();
      $table->timestamp('returned_at')->nullable();

      $table->foreign('uniform_item_id')->references('id')->on('m_igi_uniform_items');
      $table->foreign('issued_to_user_id')->references('id')->on('users');
      $table->foreign('issued_by')->references('id')->on('users');

      $table->index(['uniform_item_id', 'issued_at']);
      $table->index(['issued_to_user_id', 'status']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_uniform_issues');
  }
};
