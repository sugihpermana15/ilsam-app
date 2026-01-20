<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerCandidateController extends Controller
{
  public function index(Request $request)
  {
    $q = trim((string) $request->query('q', ''));

    $candidates = CareerCandidate::query()
      ->when($q !== '', function ($query) use ($q) {
        $query->where(function ($sub) use ($q) {
          $sub
            ->where('full_name', 'like', '%' . $q . '%')
            ->orWhere('email', 'like', '%' . $q . '%')
            ->orWhere('phone', 'like', '%' . $q . '%')
            ->orWhere('job_title', 'like', '%' . $q . '%');
        });
      })
      ->orderByDesc('created_at')
      ->paginate(15)
      ->withQueryString();

    return view('pages.admin.career.candidates_index', [
      'candidates' => $candidates,
      'q' => $q,
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
}
