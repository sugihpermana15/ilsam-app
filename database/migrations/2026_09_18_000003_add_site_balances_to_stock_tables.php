<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_item_site_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->string('site_code', 20);
            $table->unsignedInteger('on_hand_qty')->default(0);
            $table->timestamps();

            $table->unique(['stock_item_id', 'site_code']);
            $table->index(['site_code', 'stock_item_id']);
        });

        DB::table('stock_items')->orderBy('id')->each(function (object $item): void {
            DB::table('stock_item_site_balances')->insert([
                [
                    'stock_item_id' => $item->id,
                    'site_code' => 'jababeka',
                    'on_hand_qty' => (int) $item->current_stock,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'stock_item_id' => $item->id,
                    'site_code' => 'karawang',
                    'on_hand_qty' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->string('site_code', 20)->default('jababeka')->after('stock_item_id');
            $table->index(['site_code', 'trx_date']);
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropIndex(['site_code', 'trx_date']);
            $table->dropColumn('site_code');
        });

        Schema::dropIfExists('stock_item_site_balances');
    }
};
