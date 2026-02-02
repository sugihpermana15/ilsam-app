<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_igi_daily_tasks', function (Blueprint $table) {
            $table->dateTime('completed_at')->nullable()->after('updated_by');
            $table->dateTime('canceled_at')->nullable()->after('completed_at');

            $table->index(['completed_at'], 'daily_tasks_completed_at');
            $table->index(['canceled_at'], 'daily_tasks_canceled_at');
        });
    }

    public function down(): void
    {
        Schema::table('m_igi_daily_tasks', function (Blueprint $table) {
            $table->dropIndex('daily_tasks_completed_at');
            $table->dropIndex('daily_tasks_canceled_at');
            $table->dropColumn(['completed_at', 'canceled_at']);
        });
    }
};
