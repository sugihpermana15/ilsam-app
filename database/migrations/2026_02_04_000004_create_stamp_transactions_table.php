<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stamp_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('trx_no', 30)->unique();
            $table->foreignId('stamp_id')->constrained('stamps')->restrictOnDelete();
            $table->enum('trx_type', ['IN', 'OUT']);
            $table->date('trx_date');
            $table->unsignedInteger('qty');

            // PIC nullable for IN, required for OUT (enforced at validation/service layer)
            $table->foreignId('pic_id')->nullable()->constrained('m_igi_employees')->restrictOnDelete();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            // Optional but useful for audit & UX
            $table->integer('on_hand_after')->nullable();

            $table->timestamps();

            $table->index(['stamp_id', 'trx_date']);
            $table->index(['pic_id', 'trx_date']);
            $table->index(['trx_type', 'trx_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stamp_transactions');
    }
};
