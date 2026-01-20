<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('m_igi_transfer_asset', function (Blueprint $table) {
      if (!Schema::hasColumn('m_igi_transfer_asset', 'from_location')) {
        $table->string('from_location')->nullable()->after('asset_location');
      }
      if (!Schema::hasColumn('m_igi_transfer_asset', 'to_location')) {
        $table->string('to_location')->nullable()->after('from_location');
      }

      if (!Schema::hasColumn('m_igi_transfer_asset', 'status')) {
        $table->string('status', 30)->nullable()->after('asset_payload');
      }

      if (!Schema::hasColumn('m_igi_transfer_asset', 'requested_by')) {
        $table->unsignedBigInteger('requested_by')->nullable()->after('status');
      }
      if (!Schema::hasColumn('m_igi_transfer_asset', 'requested_at')) {
        $table->timestamp('requested_at')->nullable()->after('requested_by');
      }

      if (!Schema::hasColumn('m_igi_transfer_asset', 'received_by')) {
        $table->unsignedBigInteger('received_by')->nullable()->after('requested_at');
      }
      if (!Schema::hasColumn('m_igi_transfer_asset', 'received_at')) {
        $table->timestamp('received_at')->nullable()->after('received_by');
      }

      if (!Schema::hasColumn('m_igi_transfer_asset', 'cancelled_by')) {
        $table->unsignedBigInteger('cancelled_by')->nullable()->after('received_at');
      }
      if (!Schema::hasColumn('m_igi_transfer_asset', 'cancelled_at')) {
        $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
      }

      if (!Schema::hasColumn('m_igi_transfer_asset', 'transfer_notes')) {
        $table->text('transfer_notes')->nullable()->after('cancelled_at');
      }
    });

    Schema::table('m_igi_transfer_asset', function (Blueprint $table) {
      // Indexes (best-effort; safe if already exist)
      if (Schema::hasColumn('m_igi_transfer_asset', 'asset_id') && Schema::hasColumn('m_igi_transfer_asset', 'status')) {
        $table->index(['asset_id', 'status'], 'm_igi_transfer_asset_asset_id_status_idx');
      }
      if (Schema::hasColumn('m_igi_transfer_asset', 'received_at')) {
        $table->index(['received_at'], 'm_igi_transfer_asset_received_at_idx');
      }
      if (Schema::hasColumn('m_igi_transfer_asset', 'cancelled_at')) {
        $table->index(['cancelled_at'], 'm_igi_transfer_asset_cancelled_at_idx');
      }
    });
  }

  public function down(): void
  {
    Schema::table('m_igi_transfer_asset', function (Blueprint $table) {
      // Drop indexes first
      foreach (
        [
          'm_igi_transfer_asset_asset_id_status_idx',
          'm_igi_transfer_asset_received_at_idx',
          'm_igi_transfer_asset_cancelled_at_idx',
        ] as $indexName
      ) {
        try {
          $table->dropIndex($indexName);
        } catch (Throwable $e) {
          // ignore
        }
      }

      foreach (
        [
          'from_location',
          'to_location',
          'status',
          'requested_by',
          'requested_at',
          'received_by',
          'received_at',
          'cancelled_by',
          'cancelled_at',
          'transfer_notes',
        ] as $col
      ) {
        if (Schema::hasColumn('m_igi_transfer_asset', $col)) {
          $table->dropColumn($col);
        }
      }
    });
  }
};
