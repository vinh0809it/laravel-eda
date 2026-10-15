<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Events;

interface DomainEvent
{
    public function getEventType(): string;
    public function getAggregateType(): string;
    public function getAggregateId(): string;
    public function toArray(): array;
    public static function fromArray(array $data): self;
} 