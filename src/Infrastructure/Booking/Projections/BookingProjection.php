<?php

namespace Src\Infrastructure\Booking\Projections;

use Src\Infrastructure\Booking\Models\Booking;
use Src\Infrastructure\Shared\Projections\BaseProjection;
use Src\Domain\Booking\Projections\IBookingProjection;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Application\Shared\DTOs\PaginationDTO;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Application\Booking\DTOs\BookingProjectionDTO;
use Src\Domain\Shared\Loggers\IEventProcessLogger;
use Src\Domain\Booking\Exceptions\BookingNotFoundException;

class BookingProjection extends BaseProjection implements IBookingProjection
{
    public function __construct(
        Booking $model,
        private readonly IEventProcessLogger $logger
    ) {
        parent::__construct($model);
    }

    public function onBookingCreated(BookingCreated $event): void
    {
        if ($this->logger->hasProcessed($event->bookingId, self::class)) {
            return;
        }

        $this->model->create([
            'id' => $event->bookingId,
            'user_id' => $event->userId,
            'car_id' => $event->carId,  
            'start_date' => $event->startDate,
            'end_date' => $event->endDate,
            'total_price' => $event->totalPrice,
            'status' => BookingStatus::CREATED,
        ]);

        $this->logger->markSuccess($event->bookingId, self::class);
    }

    public function findByDateRange($startDate, $endDate): array
    {
        return $this->model->whereBetween('start_date', [$startDate, $endDate])
            ->where('status', BookingStatus::CREATED)
            ->get()
            ->map(function ($booking) {
                return BookingProjectionDTO::fromArray($booking->toArray());
            })
            ->all();
    }

    public function paginate(
        int $page,
        int $perPage,
        string $sortBy,
        string $sortDirection,
        array $filters = []
    ): IPaginationResult {

        $query = $this->model->newQuery();
        
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->where($key, $value);
            }
        }

        $query->orderBy($sortBy, $sortDirection);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $items = $paginator->getCollection()->map(function ($booking) {
            return BookingProjectionDTO::fromArray($booking->toArray());
        })->all();

        return new PaginationDTO(
            items: $items,
            currentPage: $paginator->currentPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
            lastPage: $paginator->lastPage()
        );
    }

    public function findById(string $bookingId): BookingProjectionDTO
    {
        $booking = $this->model->find($bookingId);

        if (!$booking) {
            throw new BookingNotFoundException(trace: ['bookingId' => $bookingId]);
        }

        return BookingProjectionDTO::fromArray($booking->toArray());
    }   
} 