<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stamp_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique();

            $table->foreignId('stamp_id')->constrained('stamps');
            $table->unsignedInteger('qty');
            $table->date('trx_date')->nullable();

            $table->unsignedBigInteger('pic_id');
            $table->foreign('pic_id')->references('id')->on('m_igi_employees');

            $table->text('notes')->nullable();

            $table->string('status', 30)->default('SUBMITTED')->index();

            $table->foreignId('requested_by')->constrained('users');
            $table->timestamp('requested_at')->useCurrent();

            $table->foreignId('validator_user_id')->nullable()->constrained('users');

            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->text('validation_notes')->nullable();

            $table->foreignId('handed_over_by')->nullable()->constrained('users');
            $table->timestamp('handed_over_at')->nullable();

            $table->foreignId('handover_trx_id')->nullable()->constrained('stamp_transactions');

            $table->timestamps();

            $table->index(['requested_by', 'status']);
            $table->index(['validator_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stamp_requests');
    }
};
