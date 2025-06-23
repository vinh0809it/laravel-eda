<?php

declare(strict_types=1);

namespace Src\Infrastructure\EventStore\Mappers;

use Src\Domain\Shared\EventStore\IEventMapper;
use Src\Infrastructure\EventStore\Map\EventMap;

class EventMapper implements IEventMapper
{
    public function resolve(string $eventShortName): string
    {
        return EventMap::resolve($eventShortName);
    }
}