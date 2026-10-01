<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_openings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 160);
            $table->string('department', 160)->nullable();
            $table->string('location', 160)->nullable();
            $table->string('type', 60)->nullable();
            $table->string('work_mode', 60)->nullable();
            $table->string('experience', 120)->nullable();
            $table->text('summary')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->string('apply_url', 500)->nullable();
            $table->date('deadline')->nullable()->index();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });

        $legacyPath = storage_path('app/private/career.json');
        if (!is_file($legacyPath)) {
            return;
        }

        $legacy = json_decode((string) file_get_contents($legacyPath), true);
        foreach (($legacy['openings'] ?? []) as $opening) {
            if (!is_array($opening) || trim((string) ($opening['title'] ?? '')) === '') {
                continue;
            }

            DB::table('career_openings')->insert([
                'id' => trim((string) ($opening['id'] ?? '')) ?: (string) Str::uuid(),
                'title' => (string) $opening['title'],
                'department' => $opening['department'] ?? null,
                'location' => $opening['location'] ?? null,
                'type' => $opening['type'] ?? null,
                'work_mode' => $opening['work_mode'] ?? null,
                'experience' => $opening['experience'] ?? null,
                'summary' => $opening['summary'] ?? null,
                'responsibilities' => $opening['responsibilities'] ?? null,
                'requirements' => $opening['requirements'] ?? null,
                'apply_url' => $opening['apply_url'] ?? null,
                'deadline' => ($opening['deadline'] ?? null) ?: null,
                'is_active' => (bool) ($opening['is_active'] ?? true),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('career_openings');
    }
};