<?php

declare(strict_types=1);

namespace App\Template\Query\Direction\GetAll;

use App\Template\ReadModel\DirectionFetcherInterface;

final class Handler
{
    public function __construct(
        private readonly DirectionFetcherInterface $fetcher
    ){
    }
    /** @return array<DirectionDTO> */
    public function handle(): array
    {
        $rows = $this->fetcher->getAll();

        return array_map(function ($row) {
            return new DirectionDTO(
                $row['id'],
                $row['title'],
                $row['description'],
                $row['text'],
                $row['slug'],
            );
        }, $rows);
    }
}