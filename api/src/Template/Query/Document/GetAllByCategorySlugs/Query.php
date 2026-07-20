<?php

declare(strict_types=1);

namespace App\Template\Query\Document\GetAllByCategorySlugs;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
        public readonly string $directionSlug,
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
        public readonly string $categorySlug,
        #[Assert\GreaterThan(0)]
        public readonly int $page = 1,
        #[Assert\GreaterThan(0)]
        public readonly int $limit = 15,
        public readonly ?string $search = null,
    ) {
    }
}
