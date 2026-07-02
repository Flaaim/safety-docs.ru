<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetBySlug;

use App\Template\ReadModel\CategoryFetcherInterface;

final class Handler
{
    public function __construct(
        private readonly CategoryFetcherInterface $fetcher,
    ) {
    }

    public function handle(Query $query): CategoryDTO
    {
        $row = $this->fetcher->getBySlugAndDirectionId($query->slug, $query->directionId);

        if (empty($row)) {
            throw new \DomainException('Category not found.');
        }

        return CategoryDTO::fromArray($row);
    }
}
