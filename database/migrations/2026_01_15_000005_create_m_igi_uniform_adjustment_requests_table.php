<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  private string $table = 'm_igi_uniform_adjustment_requests';

  private function indexExists(string $indexName): bool
  {
    $result = DB::selectOne(
      "select count(*) as c from information_schema.statistics where table_schema = database() and table_name = ? and index_name = ?",
      [$this->table, $indexName]
    );

    return (int) ($result->c ?? 0) > 0;
  }

  private function foreignKeyExists(string $column, string $referencedTable, string $referencedColumn = 'id'): bool
  {
    $result = DB::selectOne(
      "select count(*) as c from information_schema.key_column_usage where table_schema = database() and table_name = ? and column_name = ? and referenced_table_name = ? and referenced_column_name = ?",
      [$this->table, $column, $referencedTable, $referencedColumn]
    );

    return (int) ($result->c ?? 0) > 0;
  }

  public function up(): void
  {
    if (!Schema::hasTable($this->table)) {
      Schema::create($this->table, function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('uniform_item_id');
        $table->unsignedBigInteger('lot_id')->nullable();
        $table->integer('qty_change');
        $table->text('reason');
        $table->unsignedBigInteger('reference_movement_id')->nullable();

        $table->string('approval_status', 20)->default('PENDING');
        $table->unsignedBigInteger('requested_by')->nullable();
        $table->timestamp('requested_at')->useCurrent();
        $table->unsignedBigInteger('approved_by')->nullable();
        $table->timestamp('approved_at')->nullable();
        $table->text('rejection_reason')->nullable();
        $table->unsignedBigInteger('approved_movement_id')->nullable();

        $table->timestamps();

        $table->foreign('uniform_item_id', 'fk_uadj_item')->references('id')->on('m_igi_uniform_items');
        $table->foreign('lot_id', 'fk_uadj_lot')->references('id')->on('m_igi_uniform_lots');
        $table->foreign('reference_movement_id', 'fk_uadj_ref_mov')->references('id')->on('m_igi_uniform_movements');
        $table->foreign('requested_by', 'fk_uadj_req_by')->references('id')->on('users');
        $table->foreign('approved_by', 'fk_uadj_appr_by')->references('id')->on('users');
        $table->foreign('approved_movement_id', 'fk_uadj_appr_mov')->references('id')->on('m_igi_uniform_movements');

        // Explicit index names to avoid MySQL 64-char identifier limit.
        $table->index(['uniform_item_id', 'approval_status', 'requested_at'], 'idx_uadj_item_status_reqat');
        $table->index(['approval_status', 'requested_at'], 'idx_uadj_status_reqat');
      });

      return;
    }

    // Repair partially-applied migration: add missing FKs + indexes.
    Schema::table($this->table, function (Blueprint $table) {
      if (!$this->foreignKeyExists('uniform_item_id', 'm_igi_uniform_items')) {
        $table->foreign('uniform_item_id', 'fk_uadj_item')->references('id')->on('m_igi_uniform_items');
      }
      if (!$this->foreignKeyExists('lot_id', 'm_igi_uniform_lots')) {
        $table->foreign('lot_id', 'fk_uadj_lot')->references('id')->on('m_igi_uniform_lots');
      }
      if (!$this->foreignKeyExists('reference_movement_id', 'm_igi_uniform_movements')) {
        $table->foreign('reference_movement_id', 'fk_uadj_ref_mov')->references('id')->on('m_igi_uniform_movements');
      }
      if (!$this->foreignKeyExists('requested_by', 'users')) {
        $table->foreign('requested_by', 'fk_uadj_req_by')->references('id')->on('users');
      }
      if (!$this->foreignKeyExists('approved_by', 'users')) {
        $table->foreign('approved_by', 'fk_uadj_appr_by')->references('id')->on('users');
      }
      if (!$this->foreignKeyExists('approved_movement_id', 'm_igi_uniform_movements')) {
        $table->foreign('approved_movement_id', 'fk_uadj_appr_mov')->references('id')->on('m_igi_uniform_movements');
      }

      if (!$this->indexExists('idx_uadj_item_status_reqat')) {
        $table->index(['uniform_item_id', 'approval_status', 'requested_at'], 'idx_uadj_item_status_reqat');
      }
      if (!$this->indexExists('idx_uadj_status_reqat')) {
        $table->index(['approval_status', 'requested_at'], 'idx_uadj_status_reqat');
      }
    });
  }

  public function down(): void
  {
    Schema::dropIfExists($this->table);
  }
};
