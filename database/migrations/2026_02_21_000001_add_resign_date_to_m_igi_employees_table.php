<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('m_igi_employees', function (Blueprint $table) {
      $table->date('resign_date')->nullable()->after('employment_status');
    });
  }

  public function down(): void
  {
    Schema::table('m_igi_employees', function (Blueprint $table) {
      $table->dropColumn('resign_date');
    });
  }
};
