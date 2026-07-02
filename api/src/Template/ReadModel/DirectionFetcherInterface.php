<?php

namespace App\Template\ReadModel;

interface DirectionFetcherInterface
{
    public function getBySlug(string $slug): array;

    public function getAll(): array;
}
