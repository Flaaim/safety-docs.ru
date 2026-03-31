<?php

namespace App\Product\Command\GetAllFree;

use App\Product\Entity\UnassignedProductsFetcher;

class Handler
{

    public function __construct(
        private readonly UnassignedProductsFetcher $fetcher,
    ){}

    public function handle(): array
    {
        return $this->fetcher->fetchAllFree();
    }
}