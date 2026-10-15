<?php

declare(strict_types=1);

namespace Src\Booking\Application\UseCases\GetBookings;

use Src\Shared\Application\Bus\Query;

final readonly class GetBookingsQuery implements Query
{
    public function __construct(
        public string $userId,
        public int $page = 1,
        public int $perPage = 10,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?string $status = null,
    ) {}
}
