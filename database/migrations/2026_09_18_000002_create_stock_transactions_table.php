<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trx_no', 40)->unique();
            $table->foreignId('stock_item_id')->constrained('stock_items')->restrictOnDelete();
            $table->enum('trx_type', ['IN', 'OUT']);
            $table->date('trx_date');
            $table->unsignedInteger('qty');
            $table->unsignedInteger('stock_after');
            $table->foreignId('pic_id')->nullable()->constrained('m_igi_employees')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['stock_item_id', 'trx_date']);
            $table->index(['trx_type', 'trx_date']);
            $table->index(['pic_id', 'trx_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
