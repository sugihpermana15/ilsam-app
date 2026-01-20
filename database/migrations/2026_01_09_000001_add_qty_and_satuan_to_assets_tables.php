<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('m_igi_asset', function (Blueprint $table) {
      if (!Schema::hasColumn('m_igi_asset', 'qty')) {
        $table->integer('qty')->nullable()->after('price');
      }
      if (!Schema::hasColumn('m_igi_asset', 'satuan')) {
        $table->string('satuan', 50)->nullable()->after('qty');
      }
    });

    Schema::table('deleted_asset', function (Blueprint $table) {
      if (!Schema::hasColumn('deleted_asset', 'qty')) {
        $table->integer('qty')->nullable()->after('price');
      }
      if (!Schema::hasColumn('deleted_asset', 'satuan')) {
        $table->string('satuan', 50)->nullable()->after('qty');
      }
    });
  }

  public function down(): void
  {
    Schema::table('m_igi_asset', function (Blueprint $table) {
      if (Schema::hasColumn('m_igi_asset', 'satuan')) {
        $table->dropColumn('satuan');
      }
      if (Schema::hasColumn('m_igi_asset', 'qty')) {
        $table->dropColumn('qty');
      }
    });

    Schema::table('deleted_asset', function (Blueprint $table) {
      if (Schema::hasColumn('deleted_asset', 'satuan')) {
        $table->dropColumn('satuan');
      }
      if (Schema::hasColumn('deleted_asset', 'qty')) {
        $table->dropColumn('qty');
      }
    });
  }
};
