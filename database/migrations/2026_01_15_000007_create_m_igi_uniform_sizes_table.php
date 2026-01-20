<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('m_igi_uniform_sizes', function (Blueprint $table) {
      $table->id();
      $table->string('code', 50)->unique();
      $table->string('name', 100);
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index(['is_active', 'code']);
    });

    // Seed default sizes (idempotent).
    $defaults = [
      ['code' => 'XXS', 'name' => 'XXS'],
      ['code' => 'XS', 'name' => 'XS'],
      ['code' => 'S', 'name' => 'S'],
      ['code' => 'M', 'name' => 'M'],
      ['code' => 'L', 'name' => 'L'],
      ['code' => 'XL', 'name' => 'XL'],
      ['code' => 'XXL', 'name' => 'XXL'],
      ['code' => 'XXXL', 'name' => 'XXXL'],
      ['code' => '4L', 'name' => '4L'],
      ['code' => '5L', 'name' => '5L'],
      ['code' => 'FREE', 'name' => 'Free Size'],
    ];

    foreach ($defaults as $row) {
      DB::table('m_igi_uniform_sizes')->updateOrInsert(
        ['code' => $row['code']],
        [
          'name' => $row['name'],
          'is_active' => true,
          'created_at' => now(),
          'updated_at' => now(),
        ]
      );
    }
  }

  public function down(): void
  {
    Schema::dropIfExists('m_igi_uniform_sizes');
  }
};
