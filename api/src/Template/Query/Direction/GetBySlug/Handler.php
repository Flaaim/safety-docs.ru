<?php

declare(strict_types=1);

namespace App\Template\Query\Direction\GetBySlug;

use App\Template\ReadModel\DirectionFetcherInterface;

final class Handler
{
    public function __construct(
        private readonly DirectionFetcherInterface $fetcher,
    ) {
    }

    public function handle(Query $query): DirectionDTO
    {
        $row = $this->fetcher->getBySlug($query->slug);

        return new DirectionDTO(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['text'],
            $row['slug'],
            $row['categories']
        );
    }
}
