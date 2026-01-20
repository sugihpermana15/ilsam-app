<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('m_igi_uniform_writeoff_requests', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('uniform_item_id');
      $table->unsignedBigInteger('lot_id');
      $table->unsignedInteger('qty');
      $table->text('reason');

      $table->string('approval_status', 20)->default('PENDING');
      $table->unsignedBigInteger('requested_by')->nullable();
      $table->timestamp('requested_at')->useCurrent();
      $table->unsignedBigInteger('approved_by')->nullable();
      $table->timestamp('approved_at')->nullable();
      $table->text('rejection_reason')->nullable();
      $table->unsignedBigInteger('approved_movement_id')->nullable();

      $table->timestamps();

      $table->foreign('uniform_item_id')->references('id')->on('m_igi_uniform_items');
      $table->foreign('lot_id')->references('id')->on('m_igi_uniform_lots');
      $table->foreign('requested_by')->references('id')->on('users');
      $table->foreign('approved_by')->references('id')->on('users');
      $table->foreign('approved_movement_id')->references('id')->on('m_igi_uniform_movements');

      // Explicit index names to avoid MySQL 64-char identifier limit.
      $table->index(['uniform_item_id', 'approval_status', 'requested_at'], 'idx_uwr_item_status_reqat');
      $table->index(['approval_status', 'requested_at'], 'idx_uwr_status_reqat');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_uniform_writeoff_requests');
  }
};
