<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Recruitment\RecruitmentFormStoreRequest;
use App\Http\Requests\Admin\Recruitment\RecruitmentFormUpdateRequest;
use App\Models\RecruitmentForm;
use App\Services\RecruitmentFormAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class RecruitmentFormController extends Controller
{
    public function __construct(private readonly RecruitmentFormAdminService $service)
    {
    }

    public function index(): View
    {
        return view('pages.admin.recruitment.forms.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 0);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length < 0 ? 200 : $length;
        $length = max(1, min($length, 200));

        $search = trim((string) data_get($request->all(), 'search.value', ''));

        $base = RecruitmentForm::query();
        $recordsTotal = (clone $base)->count();

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $base->where(function ($q) use ($like) {
                $q->where('title', 'like', $like)
                    ->orWhere('position_name', 'like', $like)
                    ->orWhere('position_code_initial', 'like', $like);
            });
        }

        $recordsFiltered = (clone $base)->count();

        $forms = $base
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $rows = $forms->map(function (RecruitmentForm $f) {
            return [
                'id' => (int) $f->getKey(),
                'title' => (string) $f->title,
                'position_name' => (string) $f->position_name,
                'position_code_initial' => (string) $f->position_code_initial,
                'is_security_position' => (bool) $f->is_security_position,
                'is_active' => (bool) $f->is_active,
                'public_url' => URL::to(route('recruitment.form.show', ['token' => $f->public_token], false)),
                'created_at' => $f->created_at ? $f->created_at->format('Y-m-d H:i') : null,
                'actions' => [
                    'show_url' => route('admin.recruitment.forms.show', $f->getKey()),
                    'update_url' => route('admin.recruitment.forms.update', $f->getKey()),
                    'delete_url' => route('admin.recruitment.forms.destroy', $f->getKey()),
                ],
            ];
        })->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function store(RecruitmentFormStoreRequest $request): RedirectResponse
    {
        $this->service->createForm($request->validated());

        return redirect()->route('admin.recruitment.forms.index')->with('success', 'Form berhasil dibuat.');
    }

    public function show(RecruitmentForm $form): View
    {
        return view('pages.admin.recruitment.forms.show', [
            'form' => $form,
        ]);
    }

    public function update(RecruitmentFormUpdateRequest $request, RecruitmentForm $form): RedirectResponse
    {
        $this->service->updateForm($form, $request->validated());

        return redirect()->route('admin.recruitment.forms.show', $form->getKey())->with('success', 'Form berhasil diperbarui.');
    }

    public function destroy(RecruitmentForm $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.recruitment.forms.index')->with('success', 'Form berhasil dihapus.');
    }
}
