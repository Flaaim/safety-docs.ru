<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetBySlug;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $slug,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $directionId,
    ) {
    }
}
