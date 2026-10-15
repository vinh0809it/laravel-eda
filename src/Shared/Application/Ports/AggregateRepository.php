<?php

declare(strict_types=1);

namespace Src\Shared\Application\Ports;

use Src\Shared\Domain\Aggregate\AggregateRoot;

interface AggregateRepository
{
    public function save(AggregateRoot $aggregate): void;
    public function getEvents(string $aggregateType, string $aggregateId): ?array;
}