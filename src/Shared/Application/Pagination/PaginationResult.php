<?php

declare(strict_types=1);

namespace Src\Shared\Application\Pagination;

interface PaginationResult
{
    public function items(): array;
    public function currentPage(): int;
    public function perPage(): int;
    public function total(): int;
    public function lastPage(): int;
} 