<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('career_candidates', function (Blueprint $table) {
      $table->string('recruitment_stage', 40)->default('screening_cv')->after('message');
      $table->string('selection_status', 30)->default('in_process')->after('recruitment_stage');
      $table->text('stage_notes')->nullable()->after('selection_status');
      $table->boolean('is_talent_pool')->default(false)->after('stage_notes');
      $table->text('talent_pool_notes')->nullable()->after('is_talent_pool');
      $table->timestamp('processed_at')->nullable()->after('talent_pool_notes');

      $table->index(['selection_status']);
      $table->index(['is_talent_pool']);
      $table->index(['recruitment_stage']);
    });
  }

  public function down(): void
  {
    Schema::table('career_candidates', function (Blueprint $table) {
      $table->dropIndex(['selection_status']);
      $table->dropIndex(['is_talent_pool']);
      $table->dropIndex(['recruitment_stage']);
      $table->dropColumn([
        'recruitment_stage',
        'selection_status',
        'stage_notes',
        'is_talent_pool',
        'talent_pool_notes',
        'processed_at',
      ]);
    });
  }
};