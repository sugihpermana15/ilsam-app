<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add uniform_id for ASSIGNED allocations (no size/lot) and allow variant/lot to be nullable.
        if (!Schema::hasColumn('m_igi_uniform_allocation_items', 'uniform_id')) {
            DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` ADD COLUMN `uniform_id` bigint unsigned NULL AFTER `uniform_allocation_id`');
            DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` ADD INDEX `idx_alloc_uniform` (`uniform_id`)');
            DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` ADD CONSTRAINT `m_igi_uniform_allocation_items_uniform_id_foreign` FOREIGN KEY (`uniform_id`) REFERENCES `m_igi_uniforms` (`id`) ON DELETE RESTRICT');
        }

        // Drop FKs so we can change nullability.
        // Constraint names are from the current schema (SHOW CREATE TABLE).
        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` DROP FOREIGN KEY `m_igi_uniform_allocation_items_uniform_lot_id_foreign`');
        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` DROP FOREIGN KEY `m_igi_uniform_allocation_items_uniform_variant_id_foreign`');

        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` MODIFY `uniform_variant_id` bigint unsigned NULL');
        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` MODIFY `uniform_lot_id` bigint unsigned NULL');

        // Re-add FKs (nullable columns are allowed in MySQL).
        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` ADD CONSTRAINT `m_igi_uniform_allocation_items_uniform_lot_id_foreign` FOREIGN KEY (`uniform_lot_id`) REFERENCES `m_igi_uniform_lots` (`id`) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` ADD CONSTRAINT `m_igi_uniform_allocation_items_uniform_variant_id_foreign` FOREIGN KEY (`uniform_variant_id`) REFERENCES `m_igi_uniform_variants` (`id`) ON DELETE RESTRICT');
    }

    public function down(): void
    {
        // Best-effort rollback. This will fail if there are ASSIGNED rows that rely on uniform_id.
        if (Schema::hasColumn('m_igi_uniform_allocation_items', 'uniform_id')) {
            DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` DROP FOREIGN KEY `m_igi_uniform_allocation_items_uniform_id_foreign`');
            DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` DROP INDEX `idx_alloc_uniform`');
            DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` DROP COLUMN `uniform_id`');
        }

        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` DROP FOREIGN KEY `m_igi_uniform_allocation_items_uniform_lot_id_foreign`');
        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` DROP FOREIGN KEY `m_igi_uniform_allocation_items_uniform_variant_id_foreign`');

        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` MODIFY `uniform_variant_id` bigint unsigned NOT NULL');
        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` MODIFY `uniform_lot_id` bigint unsigned NOT NULL');

        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` ADD CONSTRAINT `m_igi_uniform_allocation_items_uniform_lot_id_foreign` FOREIGN KEY (`uniform_lot_id`) REFERENCES `m_igi_uniform_lots` (`id`) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE `m_igi_uniform_allocation_items` ADD CONSTRAINT `m_igi_uniform_allocation_items_uniform_variant_id_foreign` FOREIGN KEY (`uniform_variant_id`) REFERENCES `m_igi_uniform_variants` (`id`) ON DELETE RESTRICT');
    }
};
