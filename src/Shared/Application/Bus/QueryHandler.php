<?php

namespace Src\Shared\Application\Bus;

interface QueryHandler
{
    /**
     * @param Query $query
     */
    public function handle(Query $query): mixed;
}