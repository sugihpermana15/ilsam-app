<?php

use App\Enums\DailyTaskPriority;
use App\Enums\DailyTaskStatus;
use App\Enums\DailyTaskType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_igi_daily_task_types', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 80);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('m_igi_daily_task_priorities', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 40);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Statuses are intentionally fixed to preserve the status-flow rules.
        Schema::create('m_igi_daily_task_statuses', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('name', 40);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed defaults (ids match enums so existing records work without migration).
        DB::table('m_igi_daily_task_types')->insert(array_map(function ($c) {
            /** @var DailyTaskType $c */
            return [
                'id' => $c->value,
                'name' => $c->label(),
                'is_active' => true,
                'sort_order' => $c->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, DailyTaskType::cases()));

        DB::table('m_igi_daily_task_priorities')->insert(array_map(function ($c) {
            /** @var DailyTaskPriority $c */
            return [
                'id' => $c->value,
                'name' => $c->label(),
                'is_active' => true,
                'sort_order' => $c->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, DailyTaskPriority::cases()));

        DB::table('m_igi_daily_task_statuses')->insert(array_map(function ($c) {
            /** @var DailyTaskStatus $c */
            return [
                'id' => $c->value,
                'name' => $c->label(),
                'is_active' => true,
                'sort_order' => $c->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, DailyTaskStatus::cases()));
    }

    public function down(): void
    {
        Schema::dropIfExists('m_igi_daily_task_statuses');
        Schema::dropIfExists('m_igi_daily_task_priorities');
        Schema::dropIfExists('m_igi_daily_task_types');
    }
};
