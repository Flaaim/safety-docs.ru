<?php

namespace App\Template\ReadModel;

interface CategoryFetcherInterface
{
    public function getAllByDirection(string $directionId): array;
    public function getBySlugAndDirectionId(string $slug, string $directionId): array;
}