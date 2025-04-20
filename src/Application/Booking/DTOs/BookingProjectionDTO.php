<?php

namespace Src\Application\Booking\DTOs;

class BookingProjectionDTO
{
    public function __construct(
        public string $id,
        public string $carId,
        public string $userId,
        public string $startDate,
        public string $endDate,
        public float $totalPrice,
        public string $status,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'carId' => $this->carId,
            'userId' => $this->userId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalPrice' => $this->totalPrice,
            'status' => $this->status,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            carId: $data['car_id'],
            userId: $data['user_id'],
            startDate: $data['start_date'],
            endDate: $data['end_date'],
            totalPrice: $data['total_price'],
            status: $data['status'],
        );
    }
}
