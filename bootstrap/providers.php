<?php

use Src\Booking\Infrastructure\Providers\BookingServiceProvider;
use Src\Car\Infrastructure\Providers\CarServiceProvider;
use Src\Identity\Infrastructure\Providers\IdentityServiceProvider;
use Src\Pricing\Infrastructure\Providers\PricingServiceProvider;
use Src\Shared\Infrastructure\Providers\SharedServiceProvider;

return [
    SharedServiceProvider::class,
    IdentityServiceProvider::class,
    PricingServiceProvider::class,
    CarServiceProvider::class,
    BookingServiceProvider::class,
];
