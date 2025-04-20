<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Events;

interface IDomainEvent
{
    public function getEventType(): string;
    public function getAggregateType(): string;
    public function getAggregateId(): string;
    public function toArray(): array;
    public static function fromArray(array $data): self;
} 