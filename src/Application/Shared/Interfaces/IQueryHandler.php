<?php

namespace Src\Application\Shared\Interfaces;

interface IQueryHandler
{
    /**
     * @param IQuery $query
     */
    public function handle(IQuery $query): mixed;
}