<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('m_igi_asset', function (Blueprint $table) {
      if (!Schema::hasColumn('m_igi_asset', 'department_id')) {
        $table->foreignId('department_id')
          ->nullable()
          ->constrained('m_igi_departments')
          ->nullOnDelete();
      }

      if (!Schema::hasColumn('m_igi_asset', 'person_in_charge_employee_id')) {
        $table->foreignId('person_in_charge_employee_id')
          ->nullable()
          ->constrained('m_igi_employees')
          ->nullOnDelete();
      }
    });
  }

  public function down(): void
  {
    Schema::table('m_igi_asset', function (Blueprint $table) {
      if (Schema::hasColumn('m_igi_asset', 'person_in_charge_employee_id')) {
        $table->dropConstrainedForeignId('person_in_charge_employee_id');
      }
      if (Schema::hasColumn('m_igi_asset', 'department_id')) {
        $table->dropConstrainedForeignId('department_id');
      }
    });
  }
};
