<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('m_igi_uniform_items', function (Blueprint $table) {
      if (!Schema::hasColumn('m_igi_uniform_items', 'uniform_size_id')) {
        $table->unsignedBigInteger('uniform_size_id')->nullable()->after('size');
        $table->index('uniform_size_id');
        $table->foreign('uniform_size_id')->references('id')->on('m_igi_uniform_sizes');
      }
    });
  }

  public function down(): void
  {
    Schema::table('m_igi_uniform_items', function (Blueprint $table) {
      if (Schema::hasColumn('m_igi_uniform_items', 'uniform_size_id')) {
        $table->dropForeign(['uniform_size_id']);
        $table->dropIndex(['uniform_size_id']);
        $table->dropColumn('uniform_size_id');
      }
    });
  }
};
