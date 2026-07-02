<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetAllByDirection;

use App\Template\Query\Category\CategoryFetcher;

final class Handler
{
    public function __construct(
        private readonly CategoryFetcher $fetcher
    ) {
    }

    public function handle(Query $query): array
    {
        $rows = $this->fetcher->getAllByDirection($query->directionId);

        return array_map(
            static fn (array $row) => CategoryDTO::fromArray($row),
            $rows
        );
    }
}
