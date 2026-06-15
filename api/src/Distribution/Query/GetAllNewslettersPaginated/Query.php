<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetAllNewslettersPaginated;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $page,
        #[Assert\GreaterThan(0)]
        public int $perPage,
        public string $sortBy = 'created_at',
        public string $sortDir = 'DESC',
        public bool $archived = false,
    ) {
    }
}
