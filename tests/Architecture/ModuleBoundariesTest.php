<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Module boundaries
|--------------------------------------------------------------------------
|
| A module may depend on another module only through that module's public
| surface:
|
|   Src\<Module>\Contracts        interfaces + DTOs the module publishes
|   Src\<Module>\Domain\Events    events other modules may subscribe to
|
| Everything else inside a module is private. `Src\Shared` is the shared
| kernel and may be used by everyone.
|
| Domain events are public only until integration events + outbox land;
| at that point the allowed surface shrinks to Contracts alone.
*/

$modules = ['Booking', 'Car', 'Pricing', 'Identity'];

foreach ($modules as $module) {
    $others = array_values(array_diff($modules, [$module]));

    arch("{$module} only reaches other modules through their Contracts or Domain Events")
        ->expect("Src\\{$module}")
        ->not->toUse(array_map(fn ($o) => "Src\\{$o}", $others))
        ->ignoring(array_merge(
            array_map(fn ($o) => "Src\\{$o}\\Contracts", $others),
            array_map(fn ($o) => "Src\\{$o}\\Domain\\Events", $others),
        ));
}

arch('shared kernel does not depend on any module')
    ->expect('Src\Shared')
    ->not->toUse(array_map(fn ($m) => "Src\\{$m}", $modules));

arch('contracts expose only interfaces and immutable DTOs')
    ->expect(array_map(fn ($m) => "Src\\{$m}\\Contracts", ['Car', 'Pricing']))
    ->not->toUse(['Illuminate', 'Src\Car\Infrastructure', 'Src\Pricing\Infrastructure']);
