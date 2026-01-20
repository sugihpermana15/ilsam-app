<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('m_igi_uniform_lots', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('uniform_item_id');
      $table->string('lot_number', 100);
      $table->unsignedInteger('qty_in');
      $table->unsignedInteger('remaining_qty');
      $table->date('expired_at')->nullable();
      $table->timestamp('received_at')->useCurrent();
      $table->unsignedBigInteger('received_by')->nullable();
      $table->text('notes')->nullable();
      $table->timestamps();

      $table->foreign('uniform_item_id')->references('id')->on('m_igi_uniform_items');
      $table->foreign('received_by')->references('id')->on('users');

      $table->unique(['uniform_item_id', 'lot_number']);
      $table->index(['uniform_item_id', 'received_at']);
      $table->index(['uniform_item_id', 'expired_at']);
      $table->index('lot_number');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_uniform_lots');
  }
};
