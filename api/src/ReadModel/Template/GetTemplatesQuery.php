<?php

declare(strict_types=1);

namespace App\ReadModel\Template;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Query for paginated admin Template list with optional Direction/Category filters.
 */
final class GetTemplatesQuery
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        public readonly int $page = 1,
        #[Assert\GreaterThan(0)]
        #[Assert\LessThanOrEqual(100)]
        public readonly int $perPage = 20,
        #[Assert\Uuid]
        public readonly ?string $directionId = null,
        #[Assert\Uuid]
        public readonly ?string $categoryId = null,
        #[Assert\Length(max: 255)]
        public readonly ?string $search = null,
    ) {
    }
}
