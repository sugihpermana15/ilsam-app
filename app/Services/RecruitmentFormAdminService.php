<?php

namespace App\Services;

use App\Enums\RecruitmentQuestionType;
use App\Models\RecruitmentForm;
use App\Models\RecruitmentFormQuestion;
use App\Models\RecruitmentFormQuestionOption;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecruitmentFormAdminService
{
    /** @param array{title:string, position_name:string, position_code_initial:string, is_security_position?:bool, is_active?:bool} $data */
    public function createForm(array $data): RecruitmentForm
    {
        return DB::transaction(function () use ($data) {
            $userId = Auth::id();

            /** @var RecruitmentForm $form */
            $form = RecruitmentForm::query()->create([
                'title' => (string) Arr::get($data, 'title'),
                'position_name' => (string) Arr::get($data, 'position_name'),
                'position_code_initial' => (string) Arr::get($data, 'position_code_initial'),
                'is_security_position' => (bool) Arr::get($data, 'is_security_position', false),
                'is_active' => (bool) Arr::get($data, 'is_active', true),
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            return $form;
        });
    }

    /** @param array{title:string, position_name:string, position_code_initial:string, is_security_position?:bool, is_active?:bool} $data */
    public function updateForm(RecruitmentForm $form, array $data): RecruitmentForm
    {
        $userId = Auth::id();

        $form->update([
            'title' => (string) Arr::get($data, 'title'),
            'position_name' => (string) Arr::get($data, 'position_name'),
            'position_code_initial' => (string) Arr::get($data, 'position_code_initial'),
            'is_security_position' => (bool) Arr::get($data, 'is_security_position', false),
            'is_active' => (bool) Arr::get($data, 'is_active', true),
            'updated_by' => $userId,
        ]);

        return $form;
    }

    /**
     * @param array{
     *   recruitment_form_id:int,
     *   type:string,
     *   question_text:string,
     *   is_required?:bool,
     *   sort_order?:int,
     *   points?:int,
     *   options?:array<int, string|null>
     * } $data
     */
    public function addQuestion(array $data): RecruitmentFormQuestion
    {
        return DB::transaction(function () use ($data) {
            $type = (string) Arr::get($data, 'type');
            if (!in_array($type, RecruitmentQuestionType::all(), true)) {
                throw new RuntimeException('Tipe pertanyaan tidak valid.');
            }

            /** @var RecruitmentFormQuestion $q */
            $q = RecruitmentFormQuestion::query()->create([
                'recruitment_form_id' => (int) Arr::get($data, 'recruitment_form_id'),
                'type' => $type,
                'question_text' => (string) Arr::get($data, 'question_text'),
                'is_required' => (bool) Arr::get($data, 'is_required', true),
                'sort_order' => (int) Arr::get($data, 'sort_order', 0),
                'points' => (int) Arr::get($data, 'points', 0),
            ]);

            $this->syncOptions($q, Arr::get($data, 'options', []));

            return $q;
        });
    }

    /**
     * @param array{
     *   type:string,
     *   question_text:string,
     *   is_required?:bool,
     *   sort_order?:int,
     *   points?:int,
     *   options?:array<int, string|null>
     * } $data
     */
    public function updateQuestion(RecruitmentFormQuestion $question, array $data): RecruitmentFormQuestion
    {
        return DB::transaction(function () use ($question, $data) {
            $type = (string) Arr::get($data, 'type');
            if (!in_array($type, RecruitmentQuestionType::all(), true)) {
                throw new RuntimeException('Tipe pertanyaan tidak valid.');
            }

            $question->update([
                'type' => $type,
                'question_text' => (string) Arr::get($data, 'question_text'),
                'is_required' => (bool) Arr::get($data, 'is_required', true),
                'sort_order' => (int) Arr::get($data, 'sort_order', 0),
                'points' => (int) Arr::get($data, 'points', 0),
            ]);

            $this->syncOptions($question, Arr::get($data, 'options', []));

            return $question;
        });
    }

    public function deleteQuestion(RecruitmentFormQuestion $question): void
    {
        DB::transaction(function () use ($question) {
            $question->delete();
        });
    }

    /** @param array<int, string|null> $options */
    private function syncOptions(RecruitmentFormQuestion $question, array $options): void
    {
        $question->loadMissing('options');

        if ($question->type !== RecruitmentQuestionType::MULTIPLE_CHOICE) {
            RecruitmentFormQuestionOption::query()
                ->where('recruitment_form_question_id', (int) $question->getKey())
                ->delete();
            return;
        }

        $normalized = collect($options)
            ->map(fn ($v) => $v === null ? '' : trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->values();

        RecruitmentFormQuestionOption::query()
            ->where('recruitment_form_question_id', (int) $question->getKey())
            ->delete();

        foreach ($normalized as $i => $text) {
            RecruitmentFormQuestionOption::query()->create([
                'recruitment_form_question_id' => (int) $question->getKey(),
                'option_text' => (string) $text,
                'sort_order' => (int) $i,
            ]);
        }
    }
}
