<?php
declare(strict_types=1);

namespace Src\Infrastructure\EventStore\Map;

use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Infrastructure\Exceptions\UnknownEventException;

class EventMap {
    protected static array $map = [
        'BookingCreated' => BookingCreated::class,
        'BookingCompleted' => BookingCompleted::class,
    ];

    public static function resolve(string $shortName): string
    {
        if (!isset(self::$map[$shortName])) {
            throw new UnknownEventException("Unknown event type: $shortName");
        }

        return self::$map[$shortName];
    }
}
