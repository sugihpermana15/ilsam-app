<?php

namespace Database\Seeders;

use App\Models\Stamp;
use Illuminate\Database\Seeder;

class StampSeeder extends Seeder
{
    public function run(): void
    {
        Stamp::query()->firstOrCreate(
            ['code' => 'MTR-10000'],
            ['name' => 'Materai 10.000', 'face_value' => 10000, 'is_active' => true]
        );

        Stamp::query()->firstOrCreate(
            ['code' => 'MTR-6000'],
            ['name' => 'Materai 6.000', 'face_value' => 6000, 'is_active' => false]
        );
    }
}
