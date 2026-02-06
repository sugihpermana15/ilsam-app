<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_igi_uniforms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('m_igi_uniform_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_id')->constrained('m_igi_uniforms')->cascadeOnDelete();
            $table->string('size', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['uniform_id', 'size'], 'uniq_uniform_size');
            $table->index(['uniform_id', 'size'], 'idx_uniform_size');
        });

        Schema::create('m_igi_uniform_lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot_code', 60)->unique();
            $table->dateTime('received_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('received_at', 'idx_received_at');
        });

        Schema::create('m_igi_uniform_lot_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_variant_id')->constrained('m_igi_uniform_variants')->cascadeOnDelete();
            $table->foreignId('uniform_lot_id')->constrained('m_igi_uniform_lots')->cascadeOnDelete();
            $table->unsignedInteger('stock_on_hand')->default(0);
            $table->timestamps();

            $table->unique(['uniform_variant_id', 'uniform_lot_id'], 'uniq_variant_lot');
            $table->index(['uniform_variant_id', 'stock_on_hand'], 'idx_variant_on_hand');
            $table->index(['uniform_lot_id', 'stock_on_hand'], 'idx_lot_on_hand');
        });

        Schema::create('m_igi_uniform_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('allocation_no', 40)->unique();
            $table->enum('allocation_method', ['UNIVERSAL', 'ASSIGNED']);
            $table->dateTime('allocated_at');
            $table->foreignId('employee_id')->constrained('m_igi_employees')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'allocated_at'], 'idx_employee_allocated_at');
        });

        Schema::create('m_igi_uniform_allocation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uniform_allocation_id')->constrained('m_igi_uniform_allocations')->cascadeOnDelete();
            $table->foreignId('uniform_variant_id')->constrained('m_igi_uniform_variants')->restrictOnDelete();
            $table->foreignId('uniform_lot_id')->constrained('m_igi_uniform_lots')->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->timestamps();

            $table->index(['uniform_allocation_id'], 'idx_alloc');
            $table->index(['uniform_variant_id'], 'idx_alloc_variant');
            $table->index(['uniform_lot_id'], 'idx_alloc_lot');
        });

        Schema::create('m_igi_uniform_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('m_igi_employees')->cascadeOnDelete();
            $table->foreignId('uniform_id')->constrained('m_igi_uniforms')->cascadeOnDelete();
            $table->unsignedInteger('total_qty');
            $table->unsignedInteger('used_qty')->default(0);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'uniform_id'], 'uniq_employee_uniform');
            $table->index(['employee_id', 'uniform_id'], 'idx_employee_uniform');
        });

        Schema::create('m_igi_uniform_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movement_no', 40)->unique();
            $table->enum('movement_type', ['IN', 'OUT']);
            $table->dateTime('occurred_at');
            $table->foreignId('uniform_variant_id')->constrained('m_igi_uniform_variants')->restrictOnDelete();
            $table->foreignId('uniform_lot_id')->constrained('m_igi_uniform_lots')->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->unsignedInteger('stock_on_hand_after');
            $table->string('reference_type', 120)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['uniform_variant_id', 'occurred_at'], 'idx_variant_occurred');
            $table->index(['uniform_lot_id', 'occurred_at'], 'idx_lot_occurred');
            $table->index(['reference_type', 'reference_id'], 'idx_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_igi_uniform_stock_movements');
        Schema::dropIfExists('m_igi_uniform_entitlements');
        Schema::dropIfExists('m_igi_uniform_allocation_items');
        Schema::dropIfExists('m_igi_uniform_allocations');
        Schema::dropIfExists('m_igi_uniform_lot_stocks');
        Schema::dropIfExists('m_igi_uniform_lots');
        Schema::dropIfExists('m_igi_uniform_variants');
        Schema::dropIfExists('m_igi_uniforms');
    }
};
