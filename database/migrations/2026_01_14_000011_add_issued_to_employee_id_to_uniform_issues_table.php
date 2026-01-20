<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('m_igi_uniform_issues', function (Blueprint $table) {
      if (!Schema::hasColumn('m_igi_uniform_issues', 'issued_to_employee_id')) {
        $table->unsignedBigInteger('issued_to_employee_id')->nullable()->after('issued_to_user_id');
        $table->foreign('issued_to_employee_id')->references('id')->on('m_igi_employees');
        $table->index(['issued_to_employee_id', 'status']);
      }
    });
  }

  public function down(): void
  {
    Schema::table('m_igi_uniform_issues', function (Blueprint $table) {
      if (Schema::hasColumn('m_igi_uniform_issues', 'issued_to_employee_id')) {
        $table->dropForeign(['issued_to_employee_id']);
        $table->dropIndex(['issued_to_employee_id', 'status']);
        $table->dropColumn('issued_to_employee_id');
      }
    });
  }
};
