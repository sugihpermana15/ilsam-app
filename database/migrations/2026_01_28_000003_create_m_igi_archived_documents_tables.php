<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_igi_contract_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 200)->nullable();
            $table->foreignId('vendor_id')->constrained('m_igi_asset_vendors');
            $table->timestamps();

            $table->index(['vendor_id']);
        });

        Schema::create('m_igi_documents', function (Blueprint $table) {
            $table->uuid('document_id')->primary();

            $table->string('document_number', 50)->nullable()->unique();
            $table->string('document_title', 255);
            $table->string('document_type', 50);

            $table->foreignId('vendor_id')->constrained('m_igi_asset_vendors');
            $table->uuid('contract_group_id')->nullable();

            $table->foreignId('department_owner_id')->nullable()->constrained('m_igi_departments');
            $table->unsignedBigInteger('pic_user_id')->nullable();

            $table->string('status', 30)->default('Draft');
            $table->string('confidentiality_level', 20)->default('Internal');

            $table->json('tags')->nullable();
            $table->text('description')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['vendor_id', 'document_type', 'status']);
            $table->index(['contract_group_id']);
            $table->index(['confidentiality_level']);

            $table->foreign('contract_group_id')->references('id')->on('m_igi_contract_groups');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('pic_user_id')->references('id')->on('users');
        });

        Schema::create('m_igi_document_sites', function (Blueprint $table) {
            $table->id();
            $table->uuid('document_id');
            $table->foreignId('location_id')->constrained('m_igi_locations');
            $table->timestamps();

            $table->unique(['document_id', 'location_id']);
            $table->index(['location_id']);
            $table->foreign('document_id')->references('document_id')->on('m_igi_documents')->cascadeOnDelete();
        });

        Schema::create('m_igi_document_assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('document_id');
            $table->unsignedBigInteger('asset_id');
            $table->timestamps();

            $table->unique(['document_id', 'asset_id']);
            $table->index(['asset_id']);
            $table->foreign('document_id')->references('document_id')->on('m_igi_documents')->cascadeOnDelete();
            $table->foreign('asset_id')->references('id')->on('m_igi_asset');
        });

        Schema::create('m_igi_document_files', function (Blueprint $table) {
            $table->uuid('file_id')->primary();
            $table->uuid('document_id');

            $table->unsignedInteger('version_number');
            $table->string('file_name', 255);
            $table->string('file_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->string('storage_path', 500);
            $table->string('checksum', 64);

            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->nullable();

            $table->boolean('is_latest')->default(true);

            $table->timestamps();

            $table->unique(['document_id', 'version_number']);
            $table->index(['document_id', 'is_latest']);
            $table->index(['checksum']);

            $table->foreign('document_id')->references('document_id')->on('m_igi_documents')->cascadeOnDelete();
            $table->foreign('uploaded_by')->references('id')->on('users');
        });

        Schema::create('m_igi_contract_terms', function (Blueprint $table) {
            $table->uuid('document_id')->primary();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('renewal_type', 20)->nullable();
            $table->unsignedInteger('notice_period_days')->nullable();
            $table->string('billing_cycle', 20)->nullable();

            $table->decimal('contract_value', 18, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('payment_terms', 200)->nullable();

            $table->text('scope_service')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('document_id')->references('document_id')->on('m_igi_documents')->cascadeOnDelete();
            $table->index(['end_date']);
        });

        Schema::create('m_igi_document_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('related_invoice_document_id')->nullable();

            $table->date('paid_date')->nullable();
            $table->decimal('paid_amount', 18, 2)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('reference_number', 100)->nullable();

            $table->uuid('proof_document_id')->nullable();

            $table->timestamps();

            $table->foreign('related_invoice_document_id')->references('document_id')->on('m_igi_documents');
            $table->foreign('proof_document_id')->references('document_id')->on('m_igi_documents');

            $table->index(['paid_date']);
        });

        Schema::create('m_igi_document_retention_policies', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 50)->unique();
            $table->unsignedInteger('retention_years')->default(5);
            $table->boolean('auto_archive')->default(true);
            $table->boolean('auto_delete')->default(false);
            $table->timestamps();
        });

        Schema::create('m_igi_document_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('document_id');
            $table->unsignedSmallInteger('days_before');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['document_id', 'days_before', 'user_id'], 'doc_reminder_unique');
            $table->index(['user_id', 'sent_at']);

            $table->foreign('document_id')->references('document_id')->on('m_igi_documents')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_igi_document_reminder_logs');
        Schema::dropIfExists('m_igi_document_retention_policies');
        Schema::dropIfExists('m_igi_document_payments');
        Schema::dropIfExists('m_igi_contract_terms');
        Schema::dropIfExists('m_igi_document_files');
        Schema::dropIfExists('m_igi_document_assets');
        Schema::dropIfExists('m_igi_document_sites');
        Schema::dropIfExists('m_igi_documents');
        Schema::dropIfExists('m_igi_contract_groups');
    }
};
