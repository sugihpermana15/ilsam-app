<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('stock_items')
            ->where('category', 'material')
            ->where('code', 'like', 'MATERIAL%')
            ->orderBy('id')
            ->get(['id', 'code'])
            ->each(function (object $item): void {
                $code = 'MAT' . substr((string) $item->code, strlen('MATERIAL'));

                DB::table('stock_items')->where('id', $item->id)->update(['code' => $code]);
            });
    }

    public function down(): void
    {
        DB::table('stock_items')
            ->where('category', 'material')
            ->where('code', 'like', 'MAT%')
            ->orderBy('id')
            ->get(['id', 'code'])
            ->each(function (object $item): void {
                $code = 'MATERIAL' . substr((string) $item->code, strlen('MAT'));

                DB::table('stock_items')->where('id', $item->id)->update(['code' => $code]);
            });
    }
};