<?php

namespace Src\Infrastructure\Booking\ReadRepositories;

use Carbon\Carbon;
use Src\Domain\Booking\ReadRepositories\IBookingReadRepository;
use Src\Infrastructure\Shared\Repositories\BaseRepository;
use Src\Infrastructure\Booking\Models\Booking;
use Src\Domain\Shared\Interfaces\IPaginationResult;
use Src\Application\Shared\DTOs\PaginationDTO;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Booking\Exceptions\BookingNotFoundException;
use Src\Domain\Booking\Snapshots\BookingSnapshot;

class BookingReadRepository extends BaseRepository implements IBookingReadRepository
{
    public function __construct(
        Booking $model
    ) {
        parent::__construct($model);
    }
    
    public function findByDateRange(Carbon $startDate, Carbon $endDate): array
    {
        return $this->model->whereBetween('start_date', [$startDate, $endDate])
            ->where('status', BookingStatus::CREATED->value)
            ->get()
            ->map(function ($booking) {
                return BookingSnapshot::fromArray($booking->toArray());
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
            return BookingSnapshot::fromArray($booking->toArray());
        })->all();

        return new PaginationDTO(
            items: $items,
            currentPage: $paginator->currentPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
            lastPage: $paginator->lastPage()
        );
    }

    public function findById(string $bookingId): BookingSnapshot
    {
        $booking = $this->model->find($bookingId);

        if (!$booking) {
            throw new BookingNotFoundException(trace: ['bookingId' => $bookingId]);
        }

        return BookingSnapshot::fromArray($booking->toArray());
    }   
}
