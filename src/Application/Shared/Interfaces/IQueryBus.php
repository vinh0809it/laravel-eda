<?php

namespace Src\Application\Shared\Interfaces;

interface IQueryBus
{
    public function register(string $queryClass, IQueryHandler $handler): void;
    public function dispatch(IQuery $query): mixed;
}