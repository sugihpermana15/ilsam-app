<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CareerCandidate extends Model
{
  use HasFactory;

  protected $fillable = [
    'job_id',
    'job_title',
    'candidate_code',
    'full_name',
    'email',
    'phone',
    'domicile',
    'linkedin_url',
    'portfolio_url',
    'message',
    'recruitment_stage',
    'selection_status',
    'stage_notes',
    'is_talent_pool',
    'talent_pool_notes',
    'processed_at',
    'cv_path',
    'cv_original_name',
    'cv_mime',
    'cv_size',
    'ip_address',
    'user_agent',
  ];

  protected function casts(): array
  {
    return [
      'cv_size' => 'integer',
      'is_talent_pool' => 'boolean',
      'processed_at' => 'datetime',
    ];
  }

  protected static function booted(): void
  {
    static::creating(function (CareerCandidate $candidate) {
      if ($candidate->candidate_code || ($candidate->recruitment_stage ?? 'screening_cv') !== 'screening_cv') {
        return;
      }

      $baseCode = self::formatCandidateCode(
        (string) $candidate->job_title,
        (string) $candidate->full_name,
        (string) $candidate->phone,
        $candidate->domicile,
      );
      $candidate->candidate_code = self::nextAvailableCandidateCode($baseCode);
    });
  }

  public static function formatCandidateCode(string $jobTitle, string $fullName, string $phone, ?string $domicile): string
  {
    $title = self::codeComponent($jobTitle) ?: 'APPLICANT';
    $jobInitials = collect(preg_split('/\s+/', $title, -1, PREG_SPLIT_NO_EMPTY))
      ->map(fn(string $word) => Str::upper(Str::substr($word, 0, 1)))
      ->implode('');

    return implode('-', [
      Str::title(Str::lower($title)) . ' ' . $jobInitials . '.' . now()->format('y'),
      self::codeComponent($fullName),
      $title,
      preg_replace('/\D+/', '', $phone) ?: 'NOHP',
      self::codeComponent($domicile ?: 'UNKNOWN'),
    ]);
  }

  public static function nextAvailableCandidateCode(string $baseCode): string
  {
    $candidateCode = $baseCode;
    $suffix = 2;

    while (self::query()->where('candidate_code', $candidateCode)->exists()) {
      $candidateCode = $baseCode . '-' . str_pad((string) $suffix, 2, '0', STR_PAD_LEFT);
      $suffix++;
    }

    return $candidateCode;
  }

  private static function codeComponent(string $value): string
  {
    $value = preg_replace('/[^\pL\pN\s]+/u', ' ', $value) ?? '';
    return Str::upper(trim((string) preg_replace('/\s+/', ' ', $value)));
  }
}
