<?php 

declare(strict_types=1);

namespace Src\Shared\Application\Ports;

interface EventMapper
{
    public function resolve(string $eventShortName): string;
}