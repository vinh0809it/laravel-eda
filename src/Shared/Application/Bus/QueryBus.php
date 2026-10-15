<?php

declare(strict_types=1);

namespace Src\Shared\Application\Bus;

interface QueryBus
{
    public function dispatch(Query $query): mixed;
}
