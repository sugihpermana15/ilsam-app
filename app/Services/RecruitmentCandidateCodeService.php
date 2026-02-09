<?php

namespace App\Services;

use App\Models\RecruitmentForm;
use App\Models\RecruitmentFormSubmission;
use Illuminate\Support\Str;

class RecruitmentCandidateCodeService
{
    public function generateUnique(RecruitmentForm $form, string $fullName, string $positionName, string $phone): string
    {
        $base = $this->generateBase($form, $fullName, $positionName, $phone);

        $candidateCode = $base;
        $i = 2;
        while (RecruitmentFormSubmission::query()->where('candidate_code', $candidateCode)->exists()) {
            $candidateCode = $base . '-' . $i;
            $i++;
            if ($i > 99) {
                $candidateCode = $base . '-' . Str::upper(Str::random(4));
                break;
            }
        }

        return $candidateCode;
    }

    public function generateBase(RecruitmentForm $form, string $fullName, string $positionName, string $phone): string
    {
        $initial = (string) $form->position_code_initial;
        $initial = trim($initial) !== '' ? $initial : 'POS';

        $namePart = $this->normalizeSegment($fullName);
        $positionPart = $this->normalizeSegment($positionName);

        $phonePart = preg_replace('/\D+/', '', $phone) ?? '';
        $phonePart = trim($phonePart);

        $code = sprintf('%s.026-%s-%s-%s',
            $this->normalizeSegment($initial),
            $namePart,
            $positionPart,
            $phonePart
        );

        return Str::upper($code);
    }

    private function normalizeSegment(string $value): string
    {
        $v = trim($value);
        $v = preg_replace('/\s+/', '_', $v) ?? $v;
        $v = preg_replace('/[^A-Za-z0-9_\.\-]/', '', $v) ?? $v;
        $v = trim($v, '_');

        return Str::upper($v);
    }
}
