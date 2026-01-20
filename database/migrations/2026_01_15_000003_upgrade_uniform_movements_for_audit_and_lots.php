<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('m_igi_uniform_movements', function (Blueprint $table) {
      $table->unsignedBigInteger('lot_id')->nullable()->after('issue_id');
      $table->unsignedBigInteger('reference_movement_id')->nullable()->after('lot_id');
      $table->string('reference_doc', 100)->nullable()->after('reference_movement_id');

      $table->index('lot_id');
      $table->index('reference_movement_id');
    });

    // Make movement_type extensible (avoid ENUM migrations).
    // Also keep existing values.
    DB::statement("ALTER TABLE m_igi_uniform_movements MODIFY movement_type VARCHAR(30) NOT NULL");
  }

  public function down(): void
  {
    // Best-effort rollback: drop added columns/indexes. Keeping movement_type as VARCHAR (cannot safely revert to ENUM).
    Schema::table('m_igi_uniform_movements', function (Blueprint $table) {
      $table->dropIndex(['lot_id']);
      $table->dropIndex(['reference_movement_id']);
      $table->dropColumn(['lot_id', 'reference_movement_id', 'reference_doc']);
    });
  }
};
