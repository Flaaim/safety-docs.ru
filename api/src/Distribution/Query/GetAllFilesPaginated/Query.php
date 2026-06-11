<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetAllFilesPaginated;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $page,
        #[Assert\GreaterThan(0)]
        public int $perPage,
        public string $sortBy = 'date',
        public string $sortDir = 'DESC',
    ) {
    }
}
