<?php

namespace Src\Application\Car\Listeners;

use Illuminate\Support\Facades\Log;
use Src\Domain\Car\Events\PopularityFeeRecalculated;

class PopularityFeeRecalculatedListener
{
    public function handle(PopularityFeeRecalculated $event): void
    {
        Log::info('Popularity Fee Recalculated', [
            'booking_id' => $event->bookingId,
            'car_id' => $event->carId,
            'popularity_fee' => $event->newPopularityFee
        ]);

        // Here you can add more side effects:
        // - Send email confirmation
        // - Send notification
        // - Update analytics
        // - etc.
    }
}
