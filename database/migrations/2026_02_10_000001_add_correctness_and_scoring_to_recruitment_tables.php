<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruitment_form_question_options', function (Blueprint $table) {
            if (!Schema::hasColumn('recruitment_form_question_options', 'is_correct')) {
                $table->boolean('is_correct')->default(false)->after('sort_order');
                $table->index(['recruitment_form_question_id', 'is_correct'], 'rfqo_q_correct_idx');
            }
        });

        Schema::table('recruitment_form_submission_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('recruitment_form_submission_answers', 'is_correct')) {
                $table->boolean('is_correct')->nullable()->after('answer_text');
            }
            if (!Schema::hasColumn('recruitment_form_submission_answers', 'points_earned')) {
                $table->unsignedInteger('points_earned')->default(0)->after('is_correct');
                $table->index(['recruitment_form_submission_id', 'points_earned'], 'rfsa_sub_points_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_form_submission_answers', function (Blueprint $table) {
            if (Schema::hasColumn('recruitment_form_submission_answers', 'points_earned')) {
                $table->dropIndex('rfsa_sub_points_idx');
                $table->dropColumn('points_earned');
            }
            if (Schema::hasColumn('recruitment_form_submission_answers', 'is_correct')) {
                $table->dropColumn('is_correct');
            }
        });

        Schema::table('recruitment_form_question_options', function (Blueprint $table) {
            if (Schema::hasColumn('recruitment_form_question_options', 'is_correct')) {
                $table->dropIndex('rfqo_q_correct_idx');
                $table->dropColumn('is_correct');
            }
        });
    }
};
