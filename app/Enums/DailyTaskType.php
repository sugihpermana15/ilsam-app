<?php

namespace App\Enums;

enum DailyTaskType: int
{
    case General = 1;
    case Meeting = 2;
    case Report = 3;
    case FollowUp = 4;
    case Maintenance = 5;
    case Admin = 6;

    public function label(): string
    {
        return match ($this) {
            self::General => 'General',
            self::Meeting => 'Meeting',
            self::Report => 'Report',
            self::FollowUp => 'Follow Up',
            self::Maintenance => 'Maintenance',
            self::Admin => 'Admin',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $t) => ['value' => $t->value, 'label' => $t->label()],
            self::cases(),
        );
    }
}
