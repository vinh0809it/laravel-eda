<?php

declare(strict_types=1);

namespace Src\Booking\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Booking\Application\DTOs\BookingDTO;

/** @mixin BookingDTO */
final class BookingResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'car_id' => $this->carId,
            'user_id' => $this->userId,
            'start_date' => $this->startDate->toDateString(),
            'end_date' => $this->endDate->toDateString(),
            'original_price' => $this->originalPrice,
            'status' => $this->status,
            'actual_end_date' => $this->actualEndDate?->toDateString(),
            'final_price' => $this->finalPrice,
            'completion_note' => $this->completionNote,
            'canceled_at' => $this->canceledAt?->toDateTimeString(),
            'cancel_reason' => $this->cancelReason,
        ];
    }
}
