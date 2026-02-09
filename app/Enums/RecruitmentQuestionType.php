<?php

namespace App\Enums;

final class RecruitmentQuestionType
{
    public const MULTIPLE_CHOICE = 'multiple_choice';
    public const SHORT_TEXT = 'short_text';
    public const ESSAY = 'essay';

    /** @return array<int, string> */
    public static function all(): array
    {
        return [
            self::MULTIPLE_CHOICE,
            self::SHORT_TEXT,
            self::ESSAY,
        ];
    }
}
