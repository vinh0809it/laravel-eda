<?php

declare(strict_types=1);

namespace Src\Domain\Booking\ReadRepositories;

use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Domain\Shared\Repositories\IBaseRepository;

interface IBookingReadRepository extends IBaseRepository
{
    public function findByDateRange(string $startDate, string $endDate): array;
    public function paginate(int $page, int $perPage, string $sortBy, string $sortDirection, array $filters = []): IPaginationResult;
} 