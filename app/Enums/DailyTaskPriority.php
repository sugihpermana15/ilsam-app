<?php

namespace App\Enums;

enum DailyTaskPriority: int
{
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Urgent = 4;

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::Urgent => 'Urgent',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $p) => ['value' => $p->value, 'label' => $p->label()],
            self::cases(),
        );
    }
}
