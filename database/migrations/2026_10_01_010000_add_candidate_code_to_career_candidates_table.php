<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('career_candidates', function (Blueprint $table) {
            $table->string('candidate_code', 700)->nullable()->unique()->after('job_title');
        });
    }

    public function down(): void
    {
        Schema::table('career_candidates', function (Blueprint $table) {
            $table->dropUnique(['candidate_code']);
            $table->dropColumn('candidate_code');
        });
    }
};