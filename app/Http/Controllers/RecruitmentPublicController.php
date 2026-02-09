<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recruitment\CandidateProfileSubmitRequest;
use App\Http\Requests\Recruitment\CandidateTestSubmitRequest;
use App\Models\RecruitmentForm;
use App\Models\RecruitmentFormSubmission;
use App\Services\RecruitmentSubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecruitmentPublicController extends Controller
{
    public function __construct(private readonly RecruitmentSubmissionService $submissions)
    {
    }

    public function showForm(string $token): View
    {
        $form = RecruitmentForm::query()
            ->where('public_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.public.recruitment.form', [
            'form' => $form,
        ]);
    }

    public function submitProfile(CandidateProfileSubmitRequest $request, string $token): RedirectResponse
    {
        $form = RecruitmentForm::query()
            ->where('public_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validated();
        $validated['position_applied'] = (string) $form->position_name;
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = (string) $request->userAgent();

        $files = [
            'cv' => $request->file('cv'),
            'security_garda_pratama' => $request->file('security_garda_pratama'),
            'security_kta' => $request->file('security_kta'),
        ];

        $submission = $this->submissions->submitProfile($form, $validated, $files);

        return redirect()->route('recruitment.test.show', ['submissionToken' => $submission->public_token]);
    }

    public function showTest(string $submissionToken): View
    {
        $submission = RecruitmentFormSubmission::query()
            ->with(['form', 'form.questions.options'])
            ->where('public_token', $submissionToken)
            ->firstOrFail();

        $questions = $submission->form
            ? $submission->form->questions()->with('options')->orderBy('sort_order')->orderBy('id')->get()
            : collect();

        return view('pages.public.recruitment.test', [
            'submission' => $submission,
            'form' => $submission->form,
            'questions' => $questions,
        ]);
    }

    public function submitTest(CandidateTestSubmitRequest $request, string $submissionToken): RedirectResponse
    {
        $submission = RecruitmentFormSubmission::query()
            ->where('public_token', $submissionToken)
            ->firstOrFail();

        $answers = collect($request->validated()['answers'] ?? [])
            ->map(function ($row) {
                return [
                    'question_id' => (int) ($row['question_id'] ?? 0),
                    'option_id' => array_key_exists('option_id', $row) ? ($row['option_id'] !== null ? (int) $row['option_id'] : null) : null,
                    'answer_text' => $row['answer_text'] ?? null,
                ];
            })
            ->values()
            ->all();

        $this->submissions->submitTest($submission, $answers);

        return redirect()->route('recruitment.done', ['submissionToken' => $submission->public_token]);
    }

    public function done(string $submissionToken): View
    {
        $submission = RecruitmentFormSubmission::query()
            ->with('form')
            ->where('public_token', $submissionToken)
            ->firstOrFail();

        return view('pages.public.recruitment.done', [
            'submission' => $submission,
        ]);
    }
}
