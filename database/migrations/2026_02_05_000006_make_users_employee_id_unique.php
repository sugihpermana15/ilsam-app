<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'employee_id')) {
            return;
        }

        // Safety: block migration if there are duplicates already.
        $dupes = DB::table('users')
            ->select('employee_id', DB::raw('COUNT(*) as c'))
            ->whereNotNull('employee_id')
            ->groupBy('employee_id')
            ->having('c', '>', 1)
            ->limit(10)
            ->get();

        if ($dupes->isNotEmpty()) {
            $sample = $dupes
                ->map(fn ($r) => (string) $r->employee_id . ' (' . (string) $r->c . ')')
                ->implode(', ');

            throw new \RuntimeException(
                'Cannot add unique constraint: duplicate users.employee_id exist. Sample: ' . $sample .
                '. Please fix duplicates (set employee_id NULL for extra accounts or delete/merge) and re-run migration.'
            );
        }

        Schema::table('users', function (Blueprint $table) {
            // MySQL requires an index for foreign keys; drop FK first, then swap index.
            try {
                $table->dropForeign(['employee_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            // Drop non-unique index if present (created by 2026_02_02_000004_add_employee_id_to_users_table).
            try {
                $table->dropIndex('users_employee_id');
            } catch (\Throwable $e) {
                // ignore
            }

            $table->unique('employee_id', 'users_employee_id_unique');

            // Restore FK constraint.
            $table->foreign('employee_id')->references('id')->on('m_igi_employees');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'employee_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropForeign(['employee_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->dropUnique('users_employee_id_unique');
            } catch (\Throwable $e) {
                // ignore
            }

            // Restore the non-unique index name used previously.
            try {
                $table->index(['employee_id'], 'users_employee_id');
            } catch (\Throwable $e) {
                // ignore
            }

            // Restore FK constraint.
            try {
                $table->foreign('employee_id')->references('id')->on('m_igi_employees');
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};
