<?php

use Src\Infrastructure\Providers\AppServiceProvider;
use Src\Infrastructure\Providers\BookingServiceProvider;
use Src\Infrastructure\Providers\CarServiceProvider;
use Src\Infrastructure\Providers\EventServiceProvider;
use Src\Infrastructure\Providers\PriceServiceProvider;

return [
    AppServiceProvider::class,
    BookingServiceProvider::class, 
    CarServiceProvider::class,
    EventServiceProvider::class,
    PriceServiceProvider::class,
];
