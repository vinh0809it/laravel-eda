<?php

namespace Src\Domain\Booking\Enums;

enum BookingStatus: string
{
    case CREATED = 'created';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Created',
            self::CONFIRMED => 'Confirmed', 
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
        };
    }
}
