<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
  public function run(): void
  {
    $positions = [
      ['name' => 'Direktur', 'level_code' => 'DIR'],
      ['name' => 'General Manager', 'level_code' => 'GM'],
      ['name' => 'Manager', 'level_code' => 'MGR'],
      ['name' => 'Supervisor', 'level_code' => 'SUP'],
      ['name' => 'Staff', 'level_code' => 'STF'],
      ['name' => 'Operator Produksi', 'level_code' => 'OPR'],
      ['name' => 'Lab Manager', 'level_code' => 'LAB'],
      ['name' => 'Lab Analyst', 'level_code' => 'LAB'],
      ['name' => 'Lab Technician', 'level_code' => 'LAB'],
      ['name' => 'QC Analyst', 'level_code' => 'QC'],
      ['name' => 'QC Inspector', 'level_code' => 'QC'],
      ['name' => 'R&D Staff', 'level_code' => 'RD'],
    ];

    $now = now();
    $rows = array_map(fn($p) => [
      'name' => $p['name'],
      'level_code' => $p['level_code'],
      'created_at' => $now,
      'updated_at' => $now,
    ], $positions);

    DB::table('m_igi_positions')->upsert($rows, ['name'], ['level_code', 'updated_at']);
  }
}
