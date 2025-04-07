<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Interfaces;

interface IPaginationResult
{
    public function items(): array;
    public function currentPage(): int;
    public function perPage(): int;
    public function total(): int;
    public function lastPage(): int;
} 