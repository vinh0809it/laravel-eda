<?php

namespace Src\Application\Booking\Projections;

use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Booking\Events\BookingCancelled;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Booking\Repositories\IBookingRepository;
use Src\Domain\Car\Repositories\ICarRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BookingProjection
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY = 5; // seconds

    public function __construct(
        private readonly ICarRepository $carRepository,
        private readonly IBookingRepository $bookingRepository,
    ) {}

    public function onBookingCreated(BookingCreated $event): void
    {
        $this->withRetry(function () use ($event) {
            DB::beginTransaction();

            // Check if booking already exists (idempotency)
            if ($this->bookingRepository->findById($event->bookingId)) {
                Log::info('Booking already exists, skipping projection', [
                    'booking_id' => $event->bookingId
                ]);
                return;
            }

            // Create booking
            $this->bookingRepository->create([
                'id' => $event->bookingId,
                'user_id' => $event->userId,
                'car_id' => $event->carId,
                'start_date' => $event->startDate,
                'end_date' => $event->endDate,
                'total_price' => $event->totalPrice,
                'status' => 'pending',
            ]);

            // Update car availability
            $this->carRepository->updateAvailability($event->carId, false);

            DB::commit();
        });
    }
   
    private function withRetry(callable $callback): void
    {
        $retries = 0;
        while ($retries < self::MAX_RETRIES) {
            try {
                $callback();
                return;
            } catch (Exception $e) {
                DB::rollBack();
                $retries++;
                
                if ($retries >= self::MAX_RETRIES) {
                    Log::error('Failed to process booking projection after max retries', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }

                Log::warning('Retrying booking projection', [
                    'attempt' => $retries,
                    'error' => $e->getMessage()
                ]);
                
                sleep(self::RETRY_DELAY);
            }
        }
    }
} 