<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_igi_uniform_entitlements', function (Blueprint $table) {
            $table->foreignId('uniform_variant_id')
                ->nullable()
                ->after('uniform_id')
                ->constrained('m_igi_uniform_variants')
                ->nullOnDelete();

            $table->index(['employee_id', 'uniform_id', 'uniform_variant_id'], 'idx_ent_employee_uniform_variant');
        });
    }

    public function down(): void
    {
        Schema::table('m_igi_uniform_entitlements', function (Blueprint $table) {
            $table->dropIndex('idx_ent_employee_uniform_variant');
            $table->dropConstrainedForeignId('uniform_variant_id');
        });
    }
};
