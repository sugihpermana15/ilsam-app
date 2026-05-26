<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('colorants', function (Blueprint $table) {
            $table->string('bg_color')->nullable()->default('#1e3a8a');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colorants', function (Blueprint $table) {
            $table->dropColumn('bg_color');
        });
    }
};
