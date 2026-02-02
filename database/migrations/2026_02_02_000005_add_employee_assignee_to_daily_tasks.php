<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_igi_daily_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('m_igi_daily_tasks', 'assigned_employee_id')) {
                $table->unsignedBigInteger('assigned_employee_id')->nullable()->after('assigned_to');
                $table->index(['assigned_employee_id', 'status', 'due_end'], 'daily_tasks_emp_status_due');
                $table->foreign('assigned_employee_id')->references('id')->on('m_igi_employees');
            }
        });
    }

    public function down(): void
    {
        Schema::table('m_igi_daily_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('m_igi_daily_tasks', 'assigned_employee_id')) {
                $table->dropForeign(['assigned_employee_id']);
                $table->dropIndex('daily_tasks_emp_status_due');
                $table->dropColumn('assigned_employee_id');
            }
        });
    }
};
