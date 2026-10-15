<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\GetBookingById;

use Src\Shared\Application\Bus\Query;

final readonly class GetBookingByIdQuery implements Query
{
    public function __construct(
        public string $bookingId,
        public string $userId,
    ) {}
}
