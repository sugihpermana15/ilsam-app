<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('m_igi_uniform_issues', function (Blueprint $table) {
      $table->unsignedBigInteger('reference_issue_id')->nullable()->after('id');
      $table->index('reference_issue_id');
    });

    // Make status extensible (avoid ENUM migrations).
    DB::statement("ALTER TABLE m_igi_uniform_issues MODIFY status VARCHAR(20) NOT NULL DEFAULT 'ISSUED'");
  }

  public function down(): void
  {
    Schema::table('m_igi_uniform_issues', function (Blueprint $table) {
      $table->dropIndex(['reference_issue_id']);
      $table->dropColumn('reference_issue_id');
    });
  }
};
