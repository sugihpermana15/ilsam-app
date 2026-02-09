<?php

namespace App\Http\Controllers\Admin\Recruitment;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\RecruitmentForm;
use App\Models\RecruitmentFormSubmission;
use App\Models\RecruitmentFormSubmissionFile;
use App\Services\RecruitmentSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class RecruitmentCandidateController extends Controller
{
    public function __construct(private readonly RecruitmentSubmissionService $submissions)
    {
    }

    public function index(): View
    {
        return view('pages.admin.recruitment.candidates.index', [
            'forms' => RecruitmentForm::query()->orderByDesc('id')->get(['id', 'title', 'position_name']),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $search = trim((string) data_get($request->all(), 'search.value', ''));
        $filterFormId = (int) $request->input('f_form_id', 0);

        $base = RecruitmentFormSubmission::query()
            ->select('recruitment_form_submissions.*')
            ->selectSub(function ($q) {
                $q->from('recruitment_form_questions as q')
                    ->whereColumn('q.recruitment_form_id', 'recruitment_form_submissions.recruitment_form_id')
                    ->selectRaw('COALESCE(SUM(q.points), 0)');
            }, 'test_points_total')
            ->selectSub(function ($q) {
                $q->from('recruitment_form_submission_answers as a')
                    ->join('recruitment_form_questions as q', 'q.id', '=', 'a.recruitment_form_question_id')
                    ->whereColumn('a.recruitment_form_submission_id', 'recruitment_form_submissions.id')
                    ->selectRaw('COALESCE(SUM(q.points), 0)');
            }, 'test_points_earned')
            ->with('form:id,title,position_name');

        if ($filterFormId > 0) {
            $base->where('recruitment_form_id', $filterFormId);
        }

        $recordsTotal = (clone $base)->count();

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $base->where(function ($q) use ($like) {
                $q->where('candidate_code', 'like', $like)
                    ->orWhere('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('position_applied', 'like', $like);
            });
        }

        $recordsFiltered = (clone $base)->count();

        $rows = $base
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function (RecruitmentFormSubmission $s) {
                return [
                    'id' => (int) $s->getKey(),
                    'candidate_code' => (string) $s->candidate_code,
                    'full_name' => (string) $s->full_name,
                    'email' => (string) $s->email,
                    'phone' => (string) $s->phone,
                    'position_applied' => (string) $s->position_applied,
                    'form_title' => (string) ($s->form?->title ?? '-'),
                    'status' => (string) $s->status,
                    'status_label' => (string) $s->status_label,
                    'test_points_earned' => (int) ($s->test_points_earned ?? 0),
                    'test_points_total' => (int) ($s->test_points_total ?? 0),
                    'created_at' => $s->created_at ? $s->created_at->format('Y-m-d H:i') : null,
                    'actions' => [
                        'show_url' => route('admin.recruitment.candidates.show', $s->getKey()),
                    ],
                ];
            })
            ->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function show(RecruitmentFormSubmission $submission): View
    {
        $submission->loadMissing([
            'form.questions',
            'files',
            'answers.question.options',
            'answers.selectedOption',
        ]);

        return view('pages.admin.recruitment.candidates.show', [
            'submission' => $submission,
        ]);
    }

    public function downloadFile(RecruitmentFormSubmissionFile $file): mixed
    {
        $file->loadMissing('submission');

        return $this->submissions->downloadSubmissionFile($file);
    }

    public function exportPdf(RecruitmentFormSubmission $submission): Response
    {
        $submission->loadMissing([
            'form.questions.options',
            'files',
            'answers.selectedOption',
        ]);

        $filename = 'kandidat-' . ($submission->candidate_code ?: $submission->getKey()) . '.pdf';

        return Pdf::loadView('pages.admin.recruitment.candidates.export_pdf', [
            'submission' => $submission,
        ])->setPaper('a4', 'portrait')->download($filename);
    }

    public function exportExcel(RecruitmentFormSubmission $submission): Response
    {
        $submission->loadMissing([
            'form.questions.options',
            'files',
            'answers.selectedOption',
        ]);

        $filename = 'kandidat-' . ($submission->candidate_code ?: $submission->getKey()) . '.xls';

        $html = view('pages.admin.recruitment.candidates.export_excel', [
            'submission' => $submission,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
