<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $types = [
            'Email',
            'NAS',
            'Hotspot',
            'VPN',
            'Firewall',
            'Switch',
            'Server',
            'Database',
            'Domain/AD',
            'Aplikasi',
            'Other',
        ];

        foreach ($types as $name) {
            DB::table('m_igi_account_types')->updateOrInsert(
                ['name' => $name],
                ['is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('m_igi_account_types')
            ->whereIn('name', [
                'Email',
                'NAS',
                'Hotspot',
                'VPN',
                'Firewall',
                'Switch',
                'Server',
                'Database',
                'Domain/AD',
                'Aplikasi',
                'Other',
            ])
            ->delete();
    }
};
