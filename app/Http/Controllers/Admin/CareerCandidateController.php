<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerCandidate;
use App\Models\CareerEmailTemplate;
use App\Services\CareerCvAntivirusScanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CareerCandidateController extends Controller
{
  private const STAGES = [
    'screening_cv' => '1. Screening CV',
    'psychology_test' => '2. Test Psikotes',
    'hrd_online_interview' => '3. Interview HRD Online (Zoom)',
    'user_site_interview' => '4. Interview User (Visit Site)',
    'offering_letter' => '5. Offering Letter',
  ];

  private const STATUSES = [
    'in_process' => 'In Process',
    'advanced' => 'Lanjut Tahap Berikutnya',
    'rejected' => 'Tidak Lolos',
    'offered' => 'Offering Letter',
    'hired' => 'Hired',
    'talent_pool' => 'Talent Pool',
  ];

  public function screeningCv()
  {
    return $this->stageList('screening_cv');
  }

  public function psychologyTest()
  {
    return $this->stageList('psychology_test');
  }

  public function hrdOnlineInterview()
  {
    return $this->stageList('hrd_online_interview');
  }

  public function userSiteInterview()
  {
    return $this->stageList('user_site_interview');
  }

  public function offeringLetter()
  {
    return $this->stageList('offering_letter');
  }

  public function talentPool()
  {
    return view('pages.admin.career.candidates_index', [
      'stage' => null,
      'stageLabel' => 'Talent Pool',
      'isTalentPool' => true,
      'nextStage' => null,
      'nextStageLabel' => null,
    ]);
  }

  public function failedCandidates()
  {
    return view('pages.admin.career.candidates_index', [
      'stage' => null,
      'stageLabel' => 'Kandidat Gagal',
      'isTalentPool' => false,
      'isFailedCandidates' => true,
      'nextStage' => null,
      'nextStageLabel' => null,
    ]);
  }

  private function stageList(string $stage)
  {
    $stageKeys = array_keys(self::STAGES);
    $position = array_search($stage, $stageKeys, true);
    $nextStage = $position !== false ? ($stageKeys[$position + 1] ?? null) : null;

    return view('pages.admin.career.candidates_index', [
      'stage' => $stage,
      'stageLabel' => self::STAGES[$stage],
      'isTalentPool' => false,
      'nextStage' => $nextStage,
      'nextStageLabel' => $nextStage ? self::STAGES[$nextStage] : null,
    ]);
  }

  public function datatable(Request $request, string $stage): JsonResponse
  {
    abort_unless(array_key_exists($stage, self::STAGES), 404);

    return $this->datatableResponse($request, CareerCandidate::query()
      ->where('recruitment_stage', $stage)
      ->where('is_talent_pool', false)
      ->where('selection_status', '!=', 'rejected'), $stage, false);
  }

  public function talentPoolDatatable(Request $request): JsonResponse
  {
    return $this->datatableResponse($request, CareerCandidate::query()->where('is_talent_pool', true), null, true);
  }

  public function failedDatatable(Request $request): JsonResponse
  {
    return $this->datatableResponse($request, CareerCandidate::query()->where('selection_status', 'rejected'), null, false, true);
  }

  public function exportFailed()
  {
    return $this->csvDownload(
      'kandidat-gagal-' . now()->format('Ymd-His') . '.csv',
      ['Candidate Code', 'Nama', 'Email', 'No. Telepon', 'Domisili', 'Pengalaman Kerja', 'Tahap Gagal', 'Alasan Gagal', 'Diproses Pada'],
      function ($output) {
        CareerCandidate::query()
          ->where('selection_status', 'rejected')
          ->orderBy('id')
          ->chunkById(500, function ($candidates) use ($output) {
            foreach ($candidates as $candidate) {
              $this->writeCsvRow($output, [
                $candidate->candidate_code,
                $candidate->full_name,
                $candidate->email,
                $candidate->phone,
                $candidate->domicile,
                $this->experienceLabel($candidate->experience_range),
                self::STAGES[$candidate->recruitment_stage] ?? '-',
                $candidate->stage_notes,
                optional($candidate->processed_at)->format('d M Y H:i'),
              ]);
            }
          });
      },
    );
  }

  public function exportStage(string $stage)
  {
    abort_unless(array_key_exists($stage, self::STAGES), 404);
    $includesCvLink = $stage === 'psychology_test';
    $headers = ['Candidate Code', 'Nama Kandidat', 'Email', 'Posisi', 'Tahap', 'Tanggal Mendaftar'];
    if ($includesCvLink) {
      $headers[] = 'Link Download CV';
    }

    return $this->csvDownload(
      'kandidat-' . str_replace('_', '-', $stage) . '-' . now()->format('Ymd-His') . '.csv',
      $headers,
      function ($output) use ($stage, $includesCvLink) {
        CareerCandidate::query()
          ->where('recruitment_stage', $stage)
          ->where('is_talent_pool', false)
          ->where('selection_status', '!=', 'rejected')
          ->orderBy('id')
          ->chunkById(500, function ($candidates) use ($output, $stage, $includesCvLink) {
            foreach ($candidates as $candidate) {
              $values = [
                $candidate->candidate_code,
                $candidate->full_name,
                $candidate->email,
                $candidate->job_title,
                self::STAGES[$stage],
                optional($candidate->created_at)->format('d M Y H:i'),
              ];

              if ($includesCvLink) {
                $hasCv = $candidate->cv_path && Storage::disk('local')->exists($candidate->cv_path);
                $values[] = $hasCv
                  ? route('admin.career_candidates.cv', $candidate)
                  : 'CV tidak tersedia';
              }

              $this->writeCsvRow($output, $values);
            }
          });
      },
    );
  }

  private function csvDownload(string $filename, array $headers, callable $writeRows)
  {
    return response()->streamDownload(function () use ($headers, $writeRows) {
      $output = fopen('php://output', 'w');
      if ($output === false) {
        throw new \RuntimeException('Unable to open CSV output stream.');
      }

      try {
        if (fwrite($output, "\xEF\xBB\xBF") === false) {
          throw new \RuntimeException('Unable to write CSV output.');
        }
        $this->writeCsvRow($output, $headers);
        $writeRows($output);
      } finally {
        fclose($output);
      }
    }, $filename, [
      'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
  }

  private function writeCsvRow($output, array $values): void
  {
    $safeValues = array_map(fn($value) => $this->csvValue($value), $values);
    if (fputcsv($output, $safeValues, ',', '"', '') === false) {
      throw new \RuntimeException('Unable to write CSV row.');
    }
  }

  private function csvValue($value): string
  {
    $value = (string) ($value ?? '');

    return preg_match('/^[\x00-\x20]*[=+\-@]/', $value) === 1
      ? "'" . $value
      : $value;
  }

  public function emailTemplates()
  {
    return view('pages.admin.career.email_templates_index', [
      'templates' => CareerEmailTemplate::query()->orderByDesc('updated_at')->get(),
    ]);
  }

  public function storeEmailTemplate(Request $request)
  {
    CareerEmailTemplate::create($request->validate([
      'name' => ['required', 'string', 'max:120'],
      'subject' => ['required', 'string', 'max:255'],
      'body' => ['required', 'string', 'max:20000'],
    ]));

    return back()->with('success', 'Template email berhasil dibuat.');
  }

  public function updateEmailTemplate(Request $request, CareerEmailTemplate $template)
  {
    $template->update($request->validate([
      'name' => ['required', 'string', 'max:120'],
      'subject' => ['required', 'string', 'max:255'],
      'body' => ['required', 'string', 'max:20000'],
    ]));

    return back()->with('success', 'Template email berhasil diperbarui.');
  }

  public function destroyEmailTemplate(CareerEmailTemplate $template)
  {
    $template->delete();

    return back()->with('success', 'Template email berhasil dihapus.');
  }

  private function datatableResponse(Request $request, $query, ?string $stage, bool $isTalentPool, bool $isFailedCandidates = false): JsonResponse
  {
    $draw = (int) $request->input('draw', 0);
    $start = max(0, (int) $request->input('start', 0));
    $length = max(1, min((int) $request->input('length', 10), 100));
    $search = trim((string) data_get($request->all(), 'search.value', ''));

    $recordsTotal = (clone $query)->count();
    if ($search !== '') {
      $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
      $query->where(function ($sub) use ($like) {
        $sub->where('full_name', 'like', $like)
          ->orWhere('email', 'like', $like)
          ->orWhere('phone', 'like', $like)
          ->orWhere('candidate_code', 'like', $like)
          ->orWhere('job_title', 'like', $like);
      });
    }
    $recordsFiltered = (clone $query)->count();

    $rows = $query->orderByDesc('created_at')->skip($start)->take($length)->get()
      ->map(fn(CareerCandidate $candidate) => [
        'id' => $candidate->getKey(),
        'submitted_at' => optional($candidate->created_at)->format('d M Y H:i'),
        'full_name' => $candidate->full_name,
        'email' => $candidate->email,
        'candidate_code' => $candidate->candidate_code,
        'phone' => $candidate->phone,
        'job_title' => $candidate->job_title ?: '-',
        'domicile' => $candidate->domicile ?: '-',
        'current_address' => $candidate->current_address ?: '-',
        'experience_range' => $this->experienceLabel($candidate->experience_range),
        'message' => $candidate->message,
        'stage_label' => $isTalentPool ? 'Talent Pool' : (self::STAGES[$candidate->recruitment_stage] ?? '-'),
        'failed_stage_label' => self::STAGES[$candidate->recruitment_stage] ?? '-',
        'stage_notes' => $candidate->stage_notes,
        'talent_pool_notes' => $candidate->talent_pool_notes,
        'selection_status' => $candidate->selection_status,
        'has_cv' => (bool) $candidate->cv_path && Storage::disk('local')->exists($candidate->cv_path),
        'cv_view_url' => route('admin.career_candidates.cv.view', $candidate),
        'advance_url' => $stage && $candidate->selection_status !== 'rejected'
          ? route('admin.career_candidates.advance', $candidate)
          : null,
        'talent_pool_url' => $stage === 'user_site_interview' ? route('admin.career_candidates.talent_pool.store', $candidate) : null,
        'reject_url' => !$isTalentPool && $candidate->selection_status !== 'rejected'
          ? route('admin.career_candidates.reject', $candidate)
          : null,
        'delete_url' => $isFailedCandidates
          ? route('admin.career_candidates.destroy', $candidate)
          : null,
        'restore_url' => $isFailedCandidates
          ? route('admin.career_candidates.restore', $candidate)
          : null,
        'is_talent_pool' => $isTalentPool,
        'is_failed_candidate' => $isFailedCandidates,
      ])->values();

    return response()->json(compact('draw', 'recordsTotal', 'recordsFiltered') + ['data' => $rows]);
  }

  private function experienceLabel(?string $experienceRange): string
  {
    return match ($experienceRange) {
      'less_than_1' => '< 1 tahun',
      '1_to_2' => '1-2 tahun',
      '3_to_5' => '3-5 tahun',
      'more_than_5' => '> 5 tahun',
      default => '-',
    };
  }

  public function advance(CareerCandidate $candidate)
  {
    $stageKeys = array_keys(self::STAGES);
    $position = array_search($candidate->recruitment_stage, $stageKeys, true);
    $nextStage = $position !== false ? ($stageKeys[$position + 1] ?? null) : null;
    if (!$nextStage || $candidate->is_talent_pool || $candidate->selection_status === 'rejected') {
      return back()->with('error', 'Kandidat tidak dapat dipindahkan ke tahap berikutnya.');
    }

    $candidate->update([
      'recruitment_stage' => $nextStage,
      'selection_status' => $nextStage === 'offering_letter' ? 'offered' : 'in_process',
      'processed_at' => now(),
    ]);

    return back()->with('success', 'Kandidat dipindahkan ke ' . self::STAGES[$nextStage] . '.');
  }

  public function bulkAdvance(Request $request)
  {
    $validated = $request->validate([
      'stage' => ['required', 'string'],
      'candidate_ids' => ['required', 'array', 'min:1', 'max:100'],
      'candidate_ids.*' => ['integer', 'distinct'],
    ]);

    $stage = $validated['stage'];
    $stageKeys = array_keys(self::STAGES);
    $position = array_search($stage, $stageKeys, true);
    $nextStage = $position !== false ? ($stageKeys[$position + 1] ?? null) : null;
    if (!$nextStage) {
      return back()->with('error', 'Tahap ini tidak memiliki tahap lanjutan.');
    }

    $candidateIds = $validated['candidate_ids'];
    $candidates = CareerCandidate::query()
      ->whereIn('id', $candidateIds)
      ->where('recruitment_stage', $stage)
      ->where('is_talent_pool', false)
      ->where('selection_status', '!=', 'rejected')
      ->get();

    if ($candidates->count() !== count($candidateIds)) {
      return back()->with('error', 'Sebagian kandidat tidak lagi tersedia pada tahap ini. Muat ulang tabel lalu pilih kembali.');
    }

    CareerCandidate::query()->whereIn('id', $candidateIds)->update([
      'recruitment_stage' => $nextStage,
      'selection_status' => $nextStage === 'offering_letter' ? 'offered' : 'in_process',
      'processed_at' => now(),
    ]);

    return back()->with('success', count($candidateIds) . ' kandidat dipindahkan ke ' . self::STAGES[$nextStage] . '.');
  }

  public function bulkReject(Request $request)
  {
    $validated = $request->validate([
      'stage' => ['required', 'string'],
      'rejection_reason' => ['required', 'string', 'max:3000'],
      'candidate_ids' => ['required', 'array', 'min:1', 'max:100'],
      'candidate_ids.*' => ['integer', 'distinct'],
    ]);

    abort_unless(array_key_exists($validated['stage'], self::STAGES), 404);

    $candidateIds = $validated['candidate_ids'];
    $candidates = CareerCandidate::query()
      ->whereIn('id', $candidateIds)
      ->where('recruitment_stage', $validated['stage'])
      ->where('is_talent_pool', false)
      ->where('selection_status', '!=', 'rejected')
      ->get();

    if ($candidates->count() !== count($candidateIds)) {
      return back()->with('error', 'Sebagian kandidat tidak lagi tersedia pada tahap ini. Muat ulang tabel lalu pilih kembali.');
    }

    foreach ($candidates as $candidate) {
      $this->markRejected($candidate, $validated['rejection_reason']);
    }

    return back()->with('success', count($candidateIds) . ' kandidat ditandai tidak lolos. CV dan data kandidat tetap disimpan agar dapat dipulihkan.');
  }

  public function reject(Request $request, CareerCandidate $candidate)
  {
    abort_if($candidate->is_talent_pool, 422, 'Kandidat Talent Pool tidak dapat ditandai gagal.');

    $validated = $request->validate([
      'rejection_reason' => ['required', 'string', 'max:3000'],
    ]);

    $this->markRejected($candidate, $validated['rejection_reason']);

    return back()->with('success', 'Kandidat ditandai tidak lolos. CV dan data kandidat tetap disimpan agar dapat dipulihkan.');
  }

  public function restore(Request $request, CareerCandidate $candidate)
  {
    abort_unless($candidate->selection_status === 'rejected', 404);

    $hasStoredCv = $candidate->cv_path && Storage::disk('local')->exists($candidate->cv_path);
    $requiresJobTitle = blank($candidate->job_title);
    $validated = $request->validate([
      'job_title' => [$requiresJobTitle ? 'required' : 'nullable', 'string', 'max:160'],
      'cv' => [
        $hasStoredCv ? 'nullable' : 'required',
        'file',
        'max:2048',
        'mimes:pdf',
        'mimetypes:application/pdf',
      ],
    ], [
      'job_title.required' => 'Masukkan posisi kandidat sebelum dipulihkan.',
      'cv.required' => 'Unggah kembali CV PDF kandidat sebelum dipulihkan.',
      'cv.max' => 'Ukuran CV maksimal 2 MB.',
      'cv.mimes' => 'CV harus berupa file PDF.',
      'cv.mimetypes' => 'CV harus berupa file PDF.',
    ]);

    $jobTitle = trim((string) ($validated['job_title'] ?? $candidate->job_title));
    $attributes = [
      'job_title' => $jobTitle !== '' ? $jobTitle : null,
      'recruitment_stage' => 'screening_cv',
      'selection_status' => 'in_process',
      'stage_notes' => null,
      'is_talent_pool' => false,
      'talent_pool_notes' => null,
      'processed_at' => now(),
    ];

    if (!$candidate->candidate_code) {
      $baseCode = CareerCandidate::formatCandidateCode(
        $jobTitle,
        (string) $candidate->full_name,
        (string) $candidate->phone,
        $candidate->domicile,
      );
      $attributes['candidate_code'] = CareerCandidate::nextAvailableCandidateCode($baseCode);
    }

    if ($request->hasFile('cv')) {
      $cvFile = $request->file('cv');
      if ((bool) config('career_security.antivirus.enabled')) {
        $scan = app(CareerCvAntivirusScanner::class)->scan((string) $cvFile->getRealPath());
        if ($scan === 'infected') {
          return response()->json(['message' => 'CV ditolak karena terdeteksi malware.'], 422);
        }
        if ($scan === 'error' && (bool) config('career_security.antivirus.fail_closed')) {
          return response()->json(['message' => 'CV gagal dipindai. Silakan coba lagi nanti.'], 422);
        }
      }

      $originalName = trim(basename(str_replace("\0", '', (string) $cvFile->getClientOriginalName())));
      $cvPath = $cvFile->storeAs('career/cv', Str::uuid() . '.pdf', 'local');
      if ($cvPath === false) {
        return back()->with('error', 'CV gagal disimpan. Kandidat belum dipulihkan.');
      }

      $attributes += [
        'cv_path' => $cvPath,
        'cv_original_name' => $originalName !== '' ? $originalName : 'cv.pdf',
        'cv_mime' => $cvFile->getClientMimeType(),
        'cv_size' => $cvFile->getSize(),
      ];
    }

    $candidate->update($attributes);

    return back()->with('success', 'Kandidat berhasil dipulihkan ke tahap Screening CV.');
  }

  public function destroy(CareerCandidate $candidate)
  {
    abort_unless($candidate->selection_status === 'rejected', 404);

    if ($candidate->cv_path && Storage::disk('local')->exists($candidate->cv_path)) {
      Storage::disk('local')->delete($candidate->cv_path);
    }

    $candidate->delete();

    return back()->with('success', 'Data kandidat gagal telah dihapus permanen.');
  }

  public function bulkDestroy(Request $request)
  {
    $validated = $request->validate([
      'candidate_ids' => ['required', 'array', 'min:1', 'max:100'],
      'candidate_ids.*' => ['integer', 'distinct'],
    ]);

    $candidateIds = $validated['candidate_ids'];
    $candidates = CareerCandidate::query()
      ->whereIn('id', $candidateIds)
      ->where('selection_status', 'rejected')
      ->get();

    if ($candidates->count() !== count($candidateIds)) {
      return back()->with('error', 'Sebagian kandidat tidak lagi tersedia sebagai kandidat gagal. Muat ulang tabel lalu pilih kembali.');
    }

    foreach ($candidates as $candidate) {
      if ($candidate->cv_path && Storage::disk('local')->exists($candidate->cv_path)) {
        Storage::disk('local')->delete($candidate->cv_path);
      }
      $candidate->delete();
    }

    return back()->with('success', count($candidateIds) . ' data kandidat gagal telah dihapus permanen.');
  }

  public function storeTalentPool(Request $request, CareerCandidate $candidate)
  {
    if ($candidate->recruitment_stage !== 'user_site_interview') {
      abort(422, 'Talent Pool hanya tersedia setelah Interview User (Visit Site).');
    }
    $validated = $request->validate(['talent_pool_notes' => ['required', 'string', 'max:3000']]);

    $candidate->update([
      'selection_status' => 'talent_pool',
      'is_talent_pool' => true,
      'talent_pool_notes' => $validated['talent_pool_notes'],
      'processed_at' => now(),
    ]);

    return back()->with('success', 'Kandidat dipindahkan ke Talent Pool.');
  }

  private function markRejected(CareerCandidate $candidate, string $reason): void
  {
    $candidate->update([
      'selection_status' => 'rejected',
      'stage_notes' => $reason,
      'is_talent_pool' => false,
      'talent_pool_notes' => null,
      'processed_at' => now(),
    ]);
  }

  public function downloadCv(CareerCandidate $candidate)
  {
    if (!$candidate->cv_path || !Storage::disk('local')->exists($candidate->cv_path)) {
      abort(404);
    }

    $downloadName = $candidate->cv_original_name ?: ('cv-' . $candidate->id);

    $absolutePath = Storage::disk('local')->path($candidate->cv_path);

    return response()->download($absolutePath, $downloadName, [
      'Content-Type' => $candidate->cv_mime ?: 'application/octet-stream',
      'X-Content-Type-Options' => 'nosniff',
    ]);
  }

  public function viewCv(CareerCandidate $candidate)
  {
    if (!$candidate->cv_path || !Storage::disk('local')->exists($candidate->cv_path)) {
      abort(404);
    }

    return response()->file(Storage::disk('local')->path($candidate->cv_path), [
      'Content-Type' => 'application/pdf',
      'X-Content-Type-Options' => 'nosniff',
    ]);
  }
}
