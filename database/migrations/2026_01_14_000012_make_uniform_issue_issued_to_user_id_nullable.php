<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    if (!Schema::hasTable('m_igi_uniform_issues') || !Schema::hasColumn('m_igi_uniform_issues', 'issued_to_user_id')) {
      return;
    }

    $driver = DB::getDriverName();
    if ($driver !== 'mysql') {
      // Best-effort: this migration is implemented for MySQL/MariaDB.
      return;
    }

    // Drop FK, make column nullable, re-add FK with SET NULL.
    try {
      DB::statement('ALTER TABLE `m_igi_uniform_issues` DROP FOREIGN KEY `m_igi_uniform_issues_issued_to_user_id_foreign`');
    } catch (\Throwable $e) {
      // ignore if already dropped
    }

    DB::statement('ALTER TABLE `m_igi_uniform_issues` MODIFY `issued_to_user_id` BIGINT UNSIGNED NULL');

    try {
      DB::statement(
        'ALTER TABLE `m_igi_uniform_issues` ' .
          'ADD CONSTRAINT `m_igi_uniform_issues_issued_to_user_id_foreign` ' .
          'FOREIGN KEY (`issued_to_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL'
      );
    } catch (\Throwable $e) {
      // ignore if already exists
    }
  }

  public function down(): void
  {
    if (!Schema::hasTable('m_igi_uniform_issues') || !Schema::hasColumn('m_igi_uniform_issues', 'issued_to_user_id')) {
      return;
    }

    $driver = DB::getDriverName();
    if ($driver !== 'mysql') {
      return;
    }

    // Fill nulls to allow NOT NULL.
    DB::statement('UPDATE `m_igi_uniform_issues` SET `issued_to_user_id` = COALESCE(`issued_to_user_id`, `issued_by`, 1)');

    try {
      DB::statement('ALTER TABLE `m_igi_uniform_issues` DROP FOREIGN KEY `m_igi_uniform_issues_issued_to_user_id_foreign`');
    } catch (\Throwable $e) {
    }

    DB::statement('ALTER TABLE `m_igi_uniform_issues` MODIFY `issued_to_user_id` BIGINT UNSIGNED NOT NULL');

    try {
      DB::statement(
        'ALTER TABLE `m_igi_uniform_issues` ' .
          'ADD CONSTRAINT `m_igi_uniform_issues_issued_to_user_id_foreign` ' .
          'FOREIGN KEY (`issued_to_user_id`) REFERENCES `users`(`id`)'
      );
    } catch (\Throwable $e) {
    }
  }
};
