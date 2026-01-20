<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSequenceSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('m_igi_employee_sequences')->updateOrInsert(
      ['name' => 'employee'],
      ['last_value' => 0, 'updated_at' => now(), 'created_at' => now()]
    );
  }
}
