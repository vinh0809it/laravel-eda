<?php 

declare(strict_types=1);

namespace Src\Domain\Shared\EventStore;

interface IEventMapper
{
    public function resolve(string $eventShortName): string;
}