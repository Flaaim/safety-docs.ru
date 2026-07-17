<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetAllByDirectionSlug;

use App\Template\Query\Category\GetAllByDirection\CategoryDTO;
use App\Template\ReadModel\CategoryFetcherInterface;
use App\Template\ReadModel\DirectionFetcherInterface;

final class Handler
{
    public function __construct(
        private readonly DirectionFetcherInterface $directions,
        private readonly CategoryFetcherInterface $categories,
    ) {
    }

    /**
     * @return list<CategoryDTO>
     */
    public function handle(Query $query): array
    {
        $direction = $this->directions->getBySlug($query->directionSlug);

        if ($direction === []) {
            throw new \DomainException('Direction does not exist.');
        }

        $rows = $this->categories->getAllByDirection($direction['id']);

        return array_values(array_map(
            static fn (array $row) => CategoryDTO::fromArray($row),
            $rows
        ));
    }
}
