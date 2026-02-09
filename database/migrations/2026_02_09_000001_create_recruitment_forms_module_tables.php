<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // NOTE: MySQL limits identifier length to 64 chars. Some auto-generated
        // FK names for these tables can exceed that. We also drop existing
        // tables to recover cleanly if a previous migrate attempt failed mid-way.
        Schema::dropIfExists('recruitment_form_submission_files');
        Schema::dropIfExists('recruitment_form_submission_answers');
        Schema::dropIfExists('recruitment_form_submissions');
        Schema::dropIfExists('recruitment_form_question_options');
        Schema::dropIfExists('recruitment_form_questions');
        Schema::dropIfExists('recruitment_forms');

        Schema::create('recruitment_forms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('public_token')->unique();

            $table->string('title', 160);
            $table->string('position_name', 160);
            $table->string('position_code_initial', 20);
            $table->boolean('is_security_position')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['position_name']);
        });

        Schema::create('recruitment_form_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recruitment_form_id');

            $table->foreign('recruitment_form_id', 'rfq_form_fk')
                ->references('id')
                ->on('recruitment_forms')
                ->onDelete('cascade');

            $table->string('type', 30);
            $table->text('question_text');
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->unsignedInteger('points')->default(0);

            $table->timestamps();

            $table->index(['recruitment_form_id', 'sort_order'], 'rfq_form_sort_idx');
            $table->index(['type']);
        });

        Schema::create('recruitment_form_question_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recruitment_form_question_id');

            $table->foreign('recruitment_form_question_id', 'rfqo_question_fk')
                ->references('id')
                ->on('recruitment_form_questions')
                ->onDelete('cascade');

            $table->string('option_text', 255);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['recruitment_form_question_id', 'sort_order'], 'rfqo_q_sort_idx');
        });

        Schema::create('recruitment_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('public_token')->unique();

            $table->unsignedBigInteger('recruitment_form_id');

            $table->foreign('recruitment_form_id', 'rfs_form_fk')
                ->references('id')
                ->on('recruitment_forms')
                ->onDelete('cascade');

            $table->string('candidate_code', 255)->unique();

            $table->string('full_name', 160);
            $table->string('email', 200);
            $table->string('phone', 60);
            $table->string('position_applied', 160);
            $table->unsignedSmallInteger('height_cm');
            $table->unsignedSmallInteger('weight_kg');
            $table->text('address_ktp');
            $table->text('address_domicile');

            $table->string('last_education', 160)->nullable();
            $table->text('work_experience')->nullable();

            $table->string('status', 40)->default('profile_submitted');
            $table->timestamp('test_submitted_at')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['recruitment_form_id', 'created_at'], 'rfs_form_created_idx');
            $table->index(['position_applied']);
            $table->index(['status']);
        });

        Schema::create('recruitment_form_submission_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recruitment_form_submission_id');
            $table->unsignedBigInteger('recruitment_form_question_id');
            $table->unsignedBigInteger('recruitment_form_question_option_id')->nullable();

            $table->foreign('recruitment_form_submission_id', 'rfsa_submission_fk')
                ->references('id')
                ->on('recruitment_form_submissions')
                ->onDelete('cascade');

            $table->foreign('recruitment_form_question_id', 'rfsa_question_fk')
                ->references('id')
                ->on('recruitment_form_questions')
                ->onDelete('cascade');

            $table->foreign('recruitment_form_question_option_id', 'rfsa_option_fk')
                ->references('id')
                ->on('recruitment_form_question_options')
                ->onDelete('set null');

            $table->text('answer_text')->nullable();

            $table->timestamps();

            $table->unique([
                'recruitment_form_submission_id',
                'recruitment_form_question_id',
            ], 'uniq_submission_question');
        });

        Schema::create('recruitment_form_submission_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recruitment_form_submission_id');

            $table->foreign('recruitment_form_submission_id', 'rfsf_submission_fk')
                ->references('id')
                ->on('recruitment_form_submissions')
                ->onDelete('cascade');

            $table->string('field_key', 60);
            $table->string('disk', 40)->default('local');
            $table->string('storage_path', 800);

            $table->string('original_name', 255);
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();

            $table->index(['recruitment_form_submission_id', 'field_key'], 'rfsf_sub_field_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_form_submission_files');
        Schema::dropIfExists('recruitment_form_submission_answers');
        Schema::dropIfExists('recruitment_form_submissions');
        Schema::dropIfExists('recruitment_form_question_options');
        Schema::dropIfExists('recruitment_form_questions');
        Schema::dropIfExists('recruitment_forms');
    }
};
