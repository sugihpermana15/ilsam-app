<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerCandidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    $filename = 'kandidat-gagal-' . now()->format('Ymd-His') . '.csv';

    return response()->streamDownload(function () {
      $output = fopen('php://output', 'w');
      fwrite($output, "\xEF\xBB\xBF");
      fputcsv($output, ['Nama', 'Email', 'No. Telepon', 'Domisili', 'Pengalaman Kerja', 'Tahap Gagal', 'Alasan Gagal', 'Diproses Pada']);

      CareerCandidate::query()
        ->where('selection_status', 'rejected')
        ->orderByDesc('processed_at')
        ->each(function (CareerCandidate $candidate) use ($output) {
          fputcsv($output, [
            $candidate->full_name,
            $candidate->email,
            $candidate->phone,
            $candidate->domicile,
            $this->experienceLabel($candidate->experience_range),
            self::STAGES[$candidate->recruitment_stage] ?? '-',
            $candidate->stage_notes,
            optional($candidate->processed_at)->format('d M Y H:i'),
          ]);
        });

      fclose($output);
    }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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
      if (!$this->rejectAndMinimize($candidate, $validated['rejection_reason'])) {
        return back()->with('error', 'CV kandidat tidak dapat dihapus. Perubahan kandidat dibatalkan.');
      }
    }

    return back()->with('success', count($candidateIds) . ' kandidat ditandai tidak lolos. CV dihapus dan data ringkas disimpan.');
  }

  public function reject(Request $request, CareerCandidate $candidate)
  {
    abort_if($candidate->is_talent_pool, 422, 'Kandidat Talent Pool tidak dapat ditandai gagal.');

    $validated = $request->validate([
      'rejection_reason' => ['required', 'string', 'max:3000'],
    ]);

    if (!$this->rejectAndMinimize($candidate, $validated['rejection_reason'])) {
      return back()->with('error', 'CV kandidat tidak dapat dihapus. Data kandidat tetap dipertahankan.');
    }

    return back()->with('success', 'Kandidat ditandai tidak lolos. CV dihapus dan data ringkas disimpan.');
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

  private function rejectAndMinimize(CareerCandidate $candidate, string $reason): bool
  {
    if ($candidate->cv_path && Storage::disk('local')->exists($candidate->cv_path)
      && !Storage::disk('local')->delete($candidate->cv_path)) {
      return false;
    }

    $candidate->update([
      'job_id' => null,
      'job_title' => null,
      'candidate_code' => null,
      'linkedin_url' => null,
      'portfolio_url' => null,
      'message' => null,
      'cv_path' => null,
      'cv_original_name' => null,
      'cv_mime' => null,
      'cv_size' => null,
      'ip_address' => null,
      'user_agent' => null,
      'selection_status' => 'rejected',
      'stage_notes' => $reason,
      'is_talent_pool' => false,
      'talent_pool_notes' => null,
      'processed_at' => now(),
    ]);

    return true;
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
