<?php

declare(strict_types=1);

namespace Src\Booking\Infrastructure\Persistence\Eloquent;

use Carbon\Carbon;
use Src\Booking\Application\DTOs\BookingDTO;
use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Booking\Domain\Enums\BookingStatus;
use Src\Shared\Application\Pagination\PaginationDTO;
use Src\Shared\Application\Pagination\PaginationResult;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentRepository;

final class EloquentBookingReadRepository extends EloquentRepository implements BookingReadRepository
{
    public function __construct(BookingReadModel $model)
    {
        parent::__construct($model);
    }

    /**
     * Two bookings conflict when their date ranges overlap and they share
     * either the same user or the same car. Canceled bookings never conflict.
     *
     * @param string|null $excludeBookingId skip this booking (when changing its own dates)
     */
    public function hasBookingConflict(string $userId, string $carId, Carbon $startDate, Carbon $endDate, ?string $excludeBookingId = null): bool
    {
        return $this->query()
            ->where('status', '<>', BookingStatus::CANCELED->value)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->where(fn ($q) => $q->where('user_id', $userId)->orWhere('car_id', $carId))
            ->when($excludeBookingId, fn ($q) => $q->where('id', '<>', $excludeBookingId))
            ->exists();
    }

    public function paginate(int $page, int $perPage, string $sortBy, string $sortDirection, array $filters = []): PaginationResult
    {
        $query = $this->query();

        foreach ($filters as $column => $value) {
            if ($value !== null) {
                $query->where($column, $value);
            }
        }

        $paginator = $query->orderBy($sortBy, $sortDirection)->paginate($perPage, ['*'], 'page', $page);

        return new PaginationDTO(
            items: $paginator->getCollection()->map(fn ($row) => BookingDTO::fromReadModel($row->toArray()))->all(),
            currentPage: $paginator->currentPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
            lastPage: $paginator->lastPage()
        );
    }

    public function findBookingById(string $bookingId): ?BookingDTO
    {
        $row = $this->query()->find($bookingId);

        return $row ? BookingDTO::fromReadModel($row->toArray()) : null;
    }
}
