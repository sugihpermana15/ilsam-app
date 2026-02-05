<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stamp_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stamp_id')->unique()->constrained('stamps')->cascadeOnDelete();
            $table->integer('on_hand_qty')->default(0);
            $table->timestamps();

            $table->index(['stamp_id', 'on_hand_qty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stamp_balances');
    }
};
