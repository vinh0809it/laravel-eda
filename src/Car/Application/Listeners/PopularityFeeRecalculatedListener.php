<?php

declare(strict_types=1);

namespace Src\Car\Application\Listeners;

use Psr\Log\LoggerInterface;
use Src\Car\Domain\Events\PopularityFeeRecalculated;

final class PopularityFeeRecalculatedListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function handle(PopularityFeeRecalculated $event): void
    {
        $this->logger->info('Popularity Fee Recalculated', [
            'booking_id' => $event->bookingId,
            'car_id' => $event->carId,
            'popularity_fee' => $event->newPopularityFee
        ]);
    }
}
