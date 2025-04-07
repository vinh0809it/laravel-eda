<?php

namespace Src\Domain\Shared\Repositories;

interface IEventStoreRepository
{
    public function append(string $eventType, string $aggregateType, string $aggregateId, array $eventData, array $metadata = [], int $version = 1): void;
    public function getEvents(string $aggregateType, string $aggregateId): array;
    public function getLatestVersion(string $aggregateType, string $aggregateId): int;
} 