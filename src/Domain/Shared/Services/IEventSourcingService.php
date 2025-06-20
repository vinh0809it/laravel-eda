<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Services;

use Src\Domain\Shared\Aggregate\AggregateRoot;

interface IEventSourcingService
{
    public function save(AggregateRoot $aggregate): void;
    public function getEvents(string $aggregateType, string $aggregateId): ?array;
}