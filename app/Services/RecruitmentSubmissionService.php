<?php

namespace App\Services;

use App\Enums\RecruitmentQuestionType;
use App\Models\RecruitmentForm;
use App\Models\RecruitmentFormQuestion;
use App\Models\RecruitmentFormSubmission;
use App\Models\RecruitmentFormSubmissionAnswer;
use App\Models\RecruitmentFormSubmissionFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class RecruitmentSubmissionService
{
    public function __construct(private readonly RecruitmentCandidateCodeService $code)
    {
    }

    /**
     * @param array{
     *   full_name:string,
     *   email:string,
     *   phone:string,
     *   position_applied:string,
     *   height_cm:int,
     *   weight_kg:int,
     *   address_ktp:string,
     *   address_domicile:string,
     *   last_education?:string|null,
     *   work_experience?:string|null,
     *   ip_address?:string|null,
     *   user_agent?:string|null,
     * } $payload
     * @param array<string, UploadedFile|null> $files
     */
    public function submitProfile(RecruitmentForm $form, array $payload, array $files = []): RecruitmentFormSubmission
    {
        return DB::transaction(function () use ($form, $payload, $files) {
            $candidateCode = $this->code->generateUnique(
                $form,
                (string) Arr::get($payload, 'full_name'),
                (string) Arr::get($payload, 'position_applied'),
                (string) Arr::get($payload, 'phone'),
            );

            /** @var RecruitmentFormSubmission $submission */
            $submission = RecruitmentFormSubmission::query()->create([
                'recruitment_form_id' => (int) $form->getKey(),
                'candidate_code' => $candidateCode,
                'full_name' => (string) Arr::get($payload, 'full_name'),
                'email' => (string) Arr::get($payload, 'email'),
                'phone' => (string) Arr::get($payload, 'phone'),
                'position_applied' => (string) Arr::get($payload, 'position_applied'),
                'height_cm' => (int) Arr::get($payload, 'height_cm'),
                'weight_kg' => (int) Arr::get($payload, 'weight_kg'),
                'address_ktp' => (string) Arr::get($payload, 'address_ktp'),
                'address_domicile' => (string) Arr::get($payload, 'address_domicile'),
                'last_education' => Arr::get($payload, 'last_education'),
                'work_experience' => Arr::get($payload, 'work_experience'),
                'status' => 'profile_submitted',
                'ip_address' => Arr::get($payload, 'ip_address'),
                'user_agent' => Arr::get($payload, 'user_agent'),
            ]);

            $this->storeSubmissionFiles($form, $submission, $files);

            return $submission;
        });
    }

    /**
     * @param array<int, array{question_id:int, option_id?:int|null, answer_text?:string|null}> $answers
     */
    public function submitTest(RecruitmentFormSubmission $submission, array $answers): void
    {
        $submission->loadMissing('form');
        $formId = (int) $submission->recruitment_form_id;

        $allQuestions = RecruitmentFormQuestion::query()
            ->where('recruitment_form_id', $formId)
            ->get(['id', 'type', 'is_required']);

        $answersByQuestion = collect($answers)
            ->map(function ($row) {
                return [
                    'question_id' => (int) Arr::get($row, 'question_id', 0),
                    'option_id' => Arr::get($row, 'option_id'),
                    'answer_text' => Arr::get($row, 'answer_text'),
                ];
            })
            ->filter(fn ($row) => (int) $row['question_id'] > 0)
            ->keyBy('question_id');

        $missingRequired = $allQuestions
            ->where('is_required', true)
            ->filter(function ($q) use ($answersByQuestion) {
                $qid = (int) $q->id;
                $row = $answersByQuestion->get($qid);
                if (!$row) {
                    return true;
                }

                $type = (string) $q->type;
                if ($type === RecruitmentQuestionType::MULTIPLE_CHOICE) {
                    return Arr::get($row, 'option_id') === null || Arr::get($row, 'option_id') === '';
                }

                $text = Arr::get($row, 'answer_text');
                $text = $text !== null ? trim((string) $text) : '';

                return $text === '';
            });

        if ($missingRequired->isNotEmpty()) {
            throw ValidationException::withMessages([
                'answers' => 'Semua pertanyaan wajib harus dijawab.',
            ]);
        }

        DB::transaction(function () use ($submission, $answers, $formId) {
            $questionIds = collect($answers)
                ->map(fn ($a) => (int) Arr::get($a, 'question_id', 0))
                ->filter(fn ($id) => $id > 0)
                ->values();

            /** @var array<int, RecruitmentFormQuestion> $questions */
            $questions = RecruitmentFormQuestion::query()
                ->with('options')
                ->where('recruitment_form_id', $formId)
                ->whereIn('id', $questionIds)
                ->get()
                ->keyBy('id')
                ->all();

            foreach ($answers as $row) {
                $questionId = (int) Arr::get($row, 'question_id', 0);
                if ($questionId <= 0 || !isset($questions[$questionId])) {
                    continue;
                }

                $question = $questions[$questionId];
                $type = (string) $question->type;

                $optionId = Arr::get($row, 'option_id');
                $optionId = $optionId !== null ? (int) $optionId : null;
                $answerText = Arr::get($row, 'answer_text');
                $answerText = $answerText !== null ? (string) $answerText : null;

                $isCorrect = null;
                $pointsEarned = 0;

                if ($type === RecruitmentQuestionType::MULTIPLE_CHOICE) {
                    if ($optionId !== null && !$question->options->contains('id', $optionId)) {
                        throw new RuntimeException('Pilihan jawaban tidak valid.');
                    }
                    $answerText = null;

                    if ($optionId !== null) {
                        $selected = $question->options->firstWhere('id', $optionId);
                        $isCorrect = $selected ? (bool) ($selected->is_correct ?? false) : false;
                        $pointsEarned = $isCorrect ? (int) ($question->points ?? 0) : 0;
                    } else {
                        $isCorrect = false;
                        $pointsEarned = 0;
                    }
                } else {
                    $optionId = null;
                    $answerText = $answerText !== null ? trim($answerText) : null;

                    // Short text & essay are not auto-graded.
                    $isCorrect = null;
                    $pointsEarned = 0;
                }

                RecruitmentFormSubmissionAnswer::query()->updateOrCreate([
                    'recruitment_form_submission_id' => (int) $submission->getKey(),
                    'recruitment_form_question_id' => $questionId,
                ], [
                    'recruitment_form_question_option_id' => $optionId,
                    'answer_text' => $answerText,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ]);
            }

            $submission->update([
                'status' => 'test_submitted',
                'test_submitted_at' => now(),
            ]);
        });
    }

    /** @param array<string, UploadedFile|null> $files */
    private function storeSubmissionFiles(RecruitmentForm $form, RecruitmentFormSubmission $submission, array $files): void
    {
        foreach ($files as $fieldKey => $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $disk = 'local';
            $safeKey = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $fieldKey) ?? (string) $fieldKey;

            $dir = sprintf('recruitment/forms/%s/submissions/%s', (string) $form->uuid, (string) $submission->uuid);
            $name = $safeKey . '-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(8));
            $ext = $file->getClientOriginalExtension();
            $ext = $ext ? ('.' . Str::lower($ext)) : '';

            $path = $file->storeAs($dir, $name . $ext, $disk);
            if (!is_string($path) || $path === '') {
                throw new RuntimeException('Gagal menyimpan file upload.');
            }

            RecruitmentFormSubmissionFile::query()->create([
                'recruitment_form_submission_id' => (int) $submission->getKey(),
                'field_key' => (string) $safeKey,
                'disk' => $disk,
                'storage_path' => $path,
                'original_name' => (string) $file->getClientOriginalName(),
                'mime' => (string) ($file->getClientMimeType() ?? ''),
                'size' => (int) ($file->getSize() ?? 0),
            ]);
        }
    }

    public function downloadSubmissionFile(RecruitmentFormSubmissionFile $file): mixed
    {
        $disk = (string) ($file->disk ?: 'local');
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk($disk);

        if (!$storage->exists($file->storage_path)) {
            throw new RuntimeException('File tidak ditemukan.');
        }

        return $storage->download($file->storage_path, $file->original_name);
    }
}
