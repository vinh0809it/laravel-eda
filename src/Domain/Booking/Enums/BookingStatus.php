<?php

namespace Src\Domain\Booking\Enums;

enum BookingStatus: string
{
    case CREATED = 'created';
    case CHANGED = 'changed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Created',
            self::CHANGED => 'Changed', 
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
        };
    }

    public static function toArray(): array
    {
        return array_map(
            fn($case) => $case->value,
            self::cases()
        );
    }
}
