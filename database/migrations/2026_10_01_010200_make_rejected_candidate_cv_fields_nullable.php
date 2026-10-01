<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('career_candidates', function (Blueprint $table) {
            $table->string('cv_path', 500)->nullable()->change();
            $table->string('cv_original_name', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('career_candidates', function (Blueprint $table) {
            $table->string('cv_path', 500)->nullable(false)->change();
            $table->string('cv_original_name', 255)->nullable(false)->change();
        });
    }
};