<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Layer rules (per module)
|--------------------------------------------------------------------------
|
| Inside every module dependencies may only point inwards:
|   Presentation -> Application -> Domain
|   Infrastructure -> Application / Domain
|
| Remaining known debt (tracked for the Domain clean-up step): Carbon and
| HttpStatusCode inside Domain. Neither is an Illuminate dependency, so the
| rules below pass; they tighten once value objects land.
*/

$modules = ['Booking', 'Car', 'Pricing', 'Identity', 'Shared'];

foreach ($modules as $module) {
    arch("{$module}: domain does not depend on outer layers")
        ->expect("Src\\{$module}\\Domain")
        ->not->toUse([
            "Src\\{$module}\\Application",
            "Src\\{$module}\\Infrastructure",
            "Src\\{$module}\\Presentation",
        ])
        ->ignoring([
            'Src\Pricing\Domain\Services\DefaultPriceService',
            'Src\Pricing\Contracts\PriceService',
        ]);

    arch("{$module}: application does not depend on infrastructure or presentation")
        ->expect("Src\\{$module}\\Application")
        ->not->toUse([
            "Src\\{$module}\\Infrastructure",
            "Src\\{$module}\\Presentation",
        ]);
}

arch('domain does not depend on the framework')
    ->expect(array_map(fn ($m) => "Src\\{$m}\\Domain", $modules))
    ->not->toUse(['Illuminate', 'event', 'class_basename', 'now', 'app', 'config']);

arch('booking and car presentation only talk to application and domain')
    ->expect(['Src\Booking\Presentation', 'Src\Car\Presentation'])
    ->not->toUse(['Src\Booking\Infrastructure', 'Src\Car\Infrastructure']);

arch('no debug helpers in src')
    ->expect('Src')
    ->not->toUse(['dd', 'dump', 'var_dump', 'die', 'exit', 'ray']);

arch('commands and queries are immutable messages')
    ->expect('Src\Booking\Application\UseCases')
    ->classes()
    ->not->toHavePublicMethodsBesides(['__construct', 'handle'])
    ->ignoring('Src\Booking\Application\UseCases\GetBookings\GetBookingsQuery');
