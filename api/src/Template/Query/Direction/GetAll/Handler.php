<?php

declare(strict_types=1);

namespace App\Template\Query\Direction\GetAll;

use App\Template\Query\Direction\DirectionFetcher;

final class Handler
{
    public function __construct(
        private readonly DirectionFetcher $fetcher
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