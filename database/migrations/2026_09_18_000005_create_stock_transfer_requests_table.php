<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no', 40)->unique();
            $table->foreignId('stock_item_id')->constrained('stock_items')->restrictOnDelete();
            $table->string('category', 20);
            $table->unsignedInteger('qty');
            $table->string('from_site', 20)->default('jababeka');
            $table->string('to_site', 20)->default('karawang');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('requested_pic_id')->nullable()->constrained('m_igi_employees')->nullOnDelete();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone', 40)->nullable();
            $table->enum('status', ['REQUESTED', 'PREPARED', 'SHIPPED', 'RECEIVED', 'REJECTED'])->default('REQUESTED');
            $table->text('notes')->nullable();
            $table->text('shipping_notes')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('prepared_at')->nullable();
            $table->foreignId('shipped_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('shipped_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index(['to_site', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_requests');
    }
};
