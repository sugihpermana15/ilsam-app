<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    if (!Schema::hasTable('m_igi_employees')) {
      return;
    }

    $driver = DB::getDriverName();
    if ($driver !== 'mysql') {
      // Best-effort: implemented for MySQL/MariaDB.
      return;
    }

    // Make optional fields nullable.
    DB::statement('ALTER TABLE `m_igi_employees` MODIFY `birth_date` DATE NULL');
    DB::statement('ALTER TABLE `m_igi_employees` MODIFY `address` TEXT NULL');
    DB::statement('ALTER TABLE `m_igi_employees` MODIFY `phone` VARCHAR(30) NULL');
    DB::statement('ALTER TABLE `m_igi_employees` MODIFY `email` VARCHAR(255) NULL');
  }

  public function down(): void
  {
    if (!Schema::hasTable('m_igi_employees')) {
      return;
    }

    $driver = DB::getDriverName();
    if ($driver !== 'mysql') {
      return;
    }

    // Backfill nulls so NOT NULL is possible.
    DB::statement("UPDATE `m_igi_employees` SET `birth_date` = COALESCE(`birth_date`, '1970-01-01')");
    DB::statement("UPDATE `m_igi_employees` SET `address` = COALESCE(`address`, '')");
    DB::statement("UPDATE `m_igi_employees` SET `phone` = COALESCE(`phone`, '-')");
    // Email is unique; generate deterministic placeholder using id.
    DB::statement("UPDATE `m_igi_employees` SET `email` = COALESCE(`email`, CONCAT('unknown_', `id`, '@example.invalid'))");

    DB::statement('ALTER TABLE `m_igi_employees` MODIFY `birth_date` DATE NOT NULL');
    DB::statement('ALTER TABLE `m_igi_employees` MODIFY `address` TEXT NOT NULL');
    DB::statement('ALTER TABLE `m_igi_employees` MODIFY `phone` VARCHAR(30) NOT NULL');
    DB::statement('ALTER TABLE `m_igi_employees` MODIFY `email` VARCHAR(255) NOT NULL');
  }
};
