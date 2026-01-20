<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('m_igi_uniform_movements', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('uniform_item_id');
      $table->unsignedBigInteger('issue_id')->nullable();
      $table->enum('movement_type', ['IN', 'OUT', 'ADJUST']);
      $table->integer('qty_change');
      $table->string('lot_number')->nullable();
      $table->date('expired_at')->nullable();
      $table->text('notes')->nullable();
      $table->unsignedBigInteger('performed_by')->nullable();
      $table->timestamp('performed_at')->useCurrent();

      $table->foreign('uniform_item_id')->references('id')->on('m_igi_uniform_items');
      $table->foreign('issue_id')->references('id')->on('m_igi_uniform_issues');
      $table->foreign('performed_by')->references('id')->on('users');

      $table->index(['uniform_item_id', 'performed_at']);
      $table->index(['movement_type', 'performed_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_uniform_movements');
  }
};
