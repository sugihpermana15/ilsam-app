<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::table('m_igi_asset', function (Blueprint $table) {
      $table->string('image_1')->nullable()->after('notes');
      $table->string('image_2')->nullable()->after('image_1');
      $table->string('image_3')->nullable()->after('image_2');
    });
  }

  public function down()
  {
    Schema::table('m_igi_asset', function (Blueprint $table) {
      $table->dropColumn(['image_1', 'image_2', 'image_3']);
    });
  }
};
