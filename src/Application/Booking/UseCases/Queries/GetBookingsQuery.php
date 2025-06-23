<?php

declare(strict_types=1);

namespace Src\Application\Booking\UseCases\Queries;

use Src\Application\Shared\Interfaces\IQuery;

class GetBookingsQuery implements IQuery
{
    public function __construct(
        private readonly string $userId,
        private readonly ?int $page = 1,
        private readonly ?int $perPage = 10,
        private readonly ?string $sortBy = 'created_at',
        private readonly ?string $sortDirection = 'desc',
        private readonly ?string $bookingId = null,
        private readonly ?string $startDate = null,
        private readonly ?string $endDate = null,
        private readonly ?string $status = null
    ) {}

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function getBookingId(): ?string
    {
        return $this->bookingId;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
} 