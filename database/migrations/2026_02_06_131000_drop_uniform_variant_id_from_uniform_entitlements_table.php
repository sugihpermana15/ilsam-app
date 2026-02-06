<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('m_igi_uniform_entitlements', 'uniform_variant_id')) {
            return;
        }

        Schema::table('m_igi_uniform_entitlements', function (Blueprint $table) {
            // Drop index if present (created by our previous migration)
            try {
                $table->dropIndex('idx_ent_employee_uniform_variant');
            } catch (\Throwable $e) {
                // ignore
            }

            $table->dropConstrainedForeignId('uniform_variant_id');
        });
    }

    public function down(): void
    {
        Schema::table('m_igi_uniform_entitlements', function (Blueprint $table) {
            if (Schema::hasColumn('m_igi_uniform_entitlements', 'uniform_variant_id')) {
                return;
            }

            $table->foreignId('uniform_variant_id')
                ->nullable()
                ->after('uniform_id')
                ->constrained('m_igi_uniform_variants')
                ->nullOnDelete();

            $table->index(['employee_id', 'uniform_id', 'uniform_variant_id'], 'idx_ent_employee_uniform_variant');
        });
    }
};
