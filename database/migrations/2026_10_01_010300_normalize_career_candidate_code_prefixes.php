<?php

use App\Models\CareerCandidate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
  public function up(): void
  {
    CareerCandidate::query()
      ->whereNotNull('candidate_code')
      ->orderBy('id')
      ->each(function (CareerCandidate $candidate) {
        $title = trim((string) $candidate->job_title);
        $jobInitials = Str::upper(collect(preg_split('/\s+/', $title))->map(fn (string $word) => Str::substr($word, 0, 1))->implode(''));
        $oldPrefix = Str::title(Str::lower($title)) . ' ' . $jobInitials . '.';

        if ($title === '' || !Str::startsWith($candidate->candidate_code, $oldPrefix)) {
          return;
        }

        $candidate->update([
          'candidate_code' => $jobInitials . '.' . Str::after($candidate->candidate_code, $oldPrefix),
        ]);
      });
  }

  public function down(): void
  {
  }
};