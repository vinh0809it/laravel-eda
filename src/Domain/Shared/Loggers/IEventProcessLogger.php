<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Loggers;

interface IEventProcessLogger
{
    public function hasProcessed(string $eventId, string $listener): bool;

    public function markSuccess(string $eventId, string $listener): void;

    public function markFailure(string $eventId, string $listener, string $error): void;

    public function getLastError(string $eventId, string $listener): ?string;
} 