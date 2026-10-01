<?php

use App\Models\CareerCandidate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        CareerCandidate::query()
            ->whereNull('candidate_code')
            ->orderBy('id')
            ->eachById(function (CareerCandidate $candidate) {
                $baseCode = CareerCandidate::formatCandidateCode(
                    (string) $candidate->job_title,
                    (string) $candidate->full_name,
                    (string) $candidate->phone,
                    $candidate->domicile,
                );

                $candidate->update([
                    'candidate_code' => CareerCandidate::nextAvailableCandidateCode($baseCode),
                ]);
            });
    }

    public function down(): void
    {
        CareerCandidate::query()->update(['candidate_code' => null]);
    }
};