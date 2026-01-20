<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
  public function run(): void
  {
    $now = now();

    DB::table('roles')->upsert([
      ['id' => 1, 'role_name' => 'Super Admin', 'created_at' => $now, 'updated_at' => $now],
      ['id' => 2, 'role_name' => 'Admin', 'created_at' => $now, 'updated_at' => $now],
      ['id' => 3, 'role_name' => 'Users', 'created_at' => $now, 'updated_at' => $now],
    ], ['id'], ['role_name', 'updated_at']);
  }
}
