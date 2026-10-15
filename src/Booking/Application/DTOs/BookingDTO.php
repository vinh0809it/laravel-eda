<?php

declare(strict_types=1);

namespace Src\Booking\Application\DTOs;

use Carbon\Carbon;
use Src\Booking\Domain\Aggregates\BookingAggregate;

/**
 * The booking as the outside world sees it. Built either from the aggregate
 * (command results) or from a read model row (queries), so every endpoint
 * returns the same shape.
 */
final readonly class BookingDTO
{
    public function __construct(
        public string $id,
        public string $carId,
        public string $userId,
        public Carbon $startDate,
        public Carbon $endDate,
        public float $originalPrice,
        public string $status,
        public ?Carbon $actualEndDate = null,
        public ?float $finalPrice = null,
        public ?string $completionNote = null,
        public ?Carbon $canceledAt = null,
        public ?string $cancelReason = null,
    ) {}

    public static function fromAggregate(BookingAggregate $booking): self
    {
        return new self(
            id: $booking->getId(),
            carId: $booking->getCarId(),
            userId: $booking->getUserId(),
            startDate: $booking->getStartDate(),
            endDate: $booking->getEndDate(),
            originalPrice: $booking->getOriginalPrice(),
            status: $booking->getStatus(),
            actualEndDate: $booking->hasActualEndDate() ? $booking->getActualEndDate() : null,
            finalPrice: $booking->hasFinalPrice() ? $booking->getFinalPrice() : null,
            completionNote: $booking->hasCompletionNote() ? $booking->getCompletionNote() : null,
            canceledAt: $booking->hasCanceledAt() ? $booking->getCanceledAt() : null,
            cancelReason: $booking->hasCancelReason() ? $booking->getCancelReason() : null,
        );
    }

    /** @param array<string, mixed> $row a `bookings` read model row */
    public static function fromReadModel(array $row): self
    {
        return new self(
            id: (string) $row['id'],
            carId: (string) $row['car_id'],
            userId: (string) $row['user_id'],
            startDate: Carbon::parse($row['start_date']),
            endDate: Carbon::parse($row['end_date']),
            originalPrice: (float) $row['original_price'],
            status: (string) $row['status'],
            actualEndDate: isset($row['actual_end_date']) ? Carbon::parse($row['actual_end_date']) : null,
            finalPrice: isset($row['final_price']) ? (float) $row['final_price'] : null,
            completionNote: $row['completion_note'] ?? null,
            canceledAt: isset($row['canceled_at']) ? Carbon::parse($row['canceled_at']) : null,
            cancelReason: $row['cancel_reason'] ?? null,
        );
    }
}
