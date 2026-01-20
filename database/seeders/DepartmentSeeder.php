<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
  public function run(): void
  {
    $departments = [
      'HRD',
      'IT',
      'EXIM',
      'FINANCE',
      'ACCOUNTING',
      'PURCHASING',
      'MARKETING',
      'PPIC',
      'PRODUKSI',
      'LABORATORY',
      'QUALITY CONTROL',
      'QUALITY ASSURANCE',
      'R&D (Research & Development)',
    ];

    $now = now();
    $rows = array_map(fn($name) => [
      'name' => $name,
      'created_at' => $now,
      'updated_at' => $now,
    ], $departments);

    DB::table('m_igi_departments')->upsert($rows, ['name'], ['updated_at']);
  }
}
