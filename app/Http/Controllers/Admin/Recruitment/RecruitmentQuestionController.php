<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Recruitment\RecruitmentQuestionStoreRequest;
use App\Http\Requests\Admin\Recruitment\RecruitmentQuestionUpdateRequest;
use App\Models\RecruitmentFormQuestion;
use App\Services\RecruitmentFormAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RecruitmentQuestionController extends Controller
{
    public function __construct(private readonly RecruitmentFormAdminService $service)
    {
    }

    public function store(RecruitmentQuestionStoreRequest $request): RedirectResponse
    {
        $question = $this->service->addQuestion($request->validated());

        return redirect()
            ->route('admin.recruitment.forms.show', $question->recruitment_form_id)
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function update(RecruitmentQuestionUpdateRequest $request, RecruitmentFormQuestion $question): RedirectResponse
    {
        $this->service->updateQuestion($question, $request->validated());

        return redirect()
            ->route('admin.recruitment.forms.show', $question->recruitment_form_id)
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy(RecruitmentFormQuestion $question): RedirectResponse
    {
        $formId = (int) $question->recruitment_form_id;
        $this->service->deleteQuestion($question);

        return redirect()
            ->route('admin.recruitment.forms.show', $formId)
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }

    public function datatable(Request $request, int $formId): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $search = trim((string) data_get($request->all(), 'search.value', ''));

        $base = RecruitmentFormQuestion::query()->where('recruitment_form_id', $formId);
        $recordsTotal = (clone $base)->count();

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $base->where(function ($q) use ($like) {
                $q->where('type', 'like', $like)
                    ->orWhere('question_text', 'like', $like);
            });
        }

        $recordsFiltered = (clone $base)->count();

        $rows = $base
            ->with('options')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function (RecruitmentFormQuestion $q) {
                return [
                    'id' => (int) $q->getKey(),
                    'type' => (string) $q->type,
                    'type_label' => (string) $q->type_label,
                    'question_text' => (string) $q->question_text,
                    'is_required' => (bool) $q->is_required,
                    'sort_order' => (int) $q->sort_order,
                    'points' => (int) $q->points,
                    'options' => $q->options->sortBy('sort_order')->pluck('option_text')->values()->all(),
                    'actions' => [
                        'update_url' => route('admin.recruitment.questions.update', $q->getKey()),
                        'delete_url' => route('admin.recruitment.questions.destroy', $q->getKey()),
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
}
