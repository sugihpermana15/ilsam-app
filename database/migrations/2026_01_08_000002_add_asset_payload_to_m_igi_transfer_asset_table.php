<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('m_igi_transfer_asset', function (Blueprint $table) {
      // Simpan snapshot lengkap data asset agar bisa dibatalkan/restore dengan akurat
      if (!Schema::hasColumn('m_igi_transfer_asset', 'asset_payload')) {
        $table->json('asset_payload')->nullable()->after('transferred_at');
      }
    });
  }

  public function down(): void
  {
    Schema::table('m_igi_transfer_asset', function (Blueprint $table) {
      if (Schema::hasColumn('m_igi_transfer_asset', 'asset_payload')) {
        $table->dropColumn('asset_payload');
      }
    });
  }
};
