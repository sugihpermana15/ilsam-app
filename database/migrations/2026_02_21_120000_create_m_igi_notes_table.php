<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_igi_notes', function (Blueprint $table) {
            $table->id();

            $table->string('title', 255);
            $table->longText('content');
            $table->string('category', 100)->nullable();
            $table->string('tags', 255)->nullable();
            $table->string('status', 20)->default('draft');

            $table->unsignedBigInteger('created_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['created_by', 'status', 'created_at'], 'notes_creator_status_created');
            $table->index(['category', 'status'], 'notes_category_status');

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_igi_notes');
    }
};
