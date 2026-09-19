<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->after('category');
        });

        $items = DB::table('stock_items')->orderBy('id')->get();
        $sequences = [];
        foreach ($items as $item) {
            $letter = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $item->name) ?: 'X', 0, 1));
            $prefix = ($item->category === 'atk' ? 'ATK' : strtoupper(substr($item->category, 0, 3))) . $letter;
            $sequences[$prefix] = ($sequences[$prefix] ?? 0) + 1;
            DB::table('stock_items')->where('id', $item->id)->update([
                'code' => $prefix . str_pad((string) $sequences[$prefix], 3, '0', STR_PAD_LEFT),
            ]);
        }

        Schema::table('stock_items', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
