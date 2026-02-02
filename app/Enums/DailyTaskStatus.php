<?php

namespace App\Enums;

enum DailyTaskStatus: int
{
    case Todo = 1;
    case InProgress = 2;
    case Done = 3;
    case Blocked = 4;
    case Canceled = 5;

    /**
     * @return list<self>
     */
    public function allowedNext(bool $isAdmin): array
    {
        // Always allow keeping the same status.
        $base = [$this];

        return match ($this) {
            self::Todo => array_merge($base, [self::InProgress, self::Blocked, self::Canceled]),
            self::InProgress => array_merge($base, [self::Done, self::Blocked, self::Canceled]),
            self::Blocked => array_merge($base, [self::InProgress, self::Canceled]),
            self::Done => $isAdmin ? array_merge($base, [self::InProgress, self::Todo]) : $base,
            self::Canceled => $isAdmin ? array_merge($base, [self::Todo]) : $base,
        };
    }

    /**
     * @return list<int>
     */
    public static function allowedNextValues(?self $current, bool $isAdmin): array
    {
        if ($current === null) {
            return array_map(fn (self $s) => $s->value, self::cases());
        }

        return array_values(array_unique(array_map(fn (self $s) => $s->value, $current->allowedNext($isAdmin))));
    }

    public function label(): string
    {
        return match ($this) {
            self::Todo => 'To Do',
            self::InProgress => 'In Progress',
            self::Done => 'Done',
            self::Blocked => 'Blocked',
            self::Canceled => 'Canceled',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $s) => ['value' => $s->value, 'label' => $s->label()],
            self::cases(),
        );
    }
}
