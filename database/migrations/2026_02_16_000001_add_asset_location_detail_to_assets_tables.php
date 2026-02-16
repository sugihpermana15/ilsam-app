<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('m_igi_asset', function (Blueprint $table) {
      if (!Schema::hasColumn('m_igi_asset', 'asset_location_detail')) {
        $table->string('asset_location_detail')->nullable()->after('asset_location');
      }
    });

    Schema::table('deleted_asset', function (Blueprint $table) {
      if (!Schema::hasColumn('deleted_asset', 'asset_location_detail')) {
        $table->string('asset_location_detail')->nullable()->after('asset_location');
      }
    });
  }

  public function down(): void
  {
    Schema::table('m_igi_asset', function (Blueprint $table) {
      if (Schema::hasColumn('m_igi_asset', 'asset_location_detail')) {
        $table->dropColumn('asset_location_detail');
      }
    });

    Schema::table('deleted_asset', function (Blueprint $table) {
      if (Schema::hasColumn('deleted_asset', 'asset_location_detail')) {
        $table->dropColumn('asset_location_detail');
      }
    });
  }
};
