<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    if (!Schema::hasTable('m_igi_asset')) {
      return;
    }

    Schema::table('m_igi_asset', function (Blueprint $table) {
      if (!Schema::hasColumn('m_igi_asset', 'image_1')) {
        $table->string('image_1')->nullable()->after('notes');
      }
      if (!Schema::hasColumn('m_igi_asset', 'image_2')) {
        $table->string('image_2')->nullable()->after('image_1');
      }
      if (!Schema::hasColumn('m_igi_asset', 'image_3')) {
        $table->string('image_3')->nullable()->after('image_2');
      }
    });
  }

  public function down()
  {
    if (!Schema::hasTable('m_igi_asset')) {
      return;
    }

    Schema::table('m_igi_asset', function (Blueprint $table) {
      $columns = array_filter(['image_1', 'image_2', 'image_3'], fn (string $column) => Schema::hasColumn('m_igi_asset', $column));
      if ($columns) {
        $table->dropColumn($columns);
      }
    });
  }
};
