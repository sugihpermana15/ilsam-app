<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_igi_daily_tasks', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('task_type')->default(1);
            $table->string('title', 200);
            $table->string('description_preview', 255)->nullable();
            $table->text('description')->nullable();

            $table->date('due_start')->nullable();
            $table->date('due_end')->nullable();

            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedTinyInteger('priority')->default(2);

            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['assigned_to', 'status', 'due_end'], 'daily_tasks_assignee_status_due');
            $table->index(['created_by', 'updated_at'], 'daily_tasks_creator_updated');
            $table->index(['status', 'priority'], 'daily_tasks_status_priority');
            $table->index(['due_end'], 'daily_tasks_due_end');

            $table->foreign('assigned_to')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        Schema::create('m_igi_daily_task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_task_id')->constrained('m_igi_daily_tasks')->cascadeOnDelete();

            $table->string('disk', 20)->default('public');
            $table->string('storage_path', 500);
            $table->string('file_name', 255);
            $table->string('file_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();

            $table->timestamps();

            $table->index(['daily_task_id'], 'daily_task_attachments_task');
            $table->index(['uploaded_by'], 'daily_task_attachments_uploader');

            $table->foreign('uploaded_by')->references('id')->on('users');
        });

        Schema::create('m_igi_daily_task_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_task_id')->constrained('m_igi_daily_tasks')->cascadeOnDelete();

            $table->string('item_text', 255);
            $table->boolean('is_done')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->index(['daily_task_id', 'is_done'], 'daily_task_checklist_task_done');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_igi_daily_task_checklist_items');
        Schema::dropIfExists('m_igi_daily_task_attachments');
        Schema::dropIfExists('m_igi_daily_tasks');
    }
};
