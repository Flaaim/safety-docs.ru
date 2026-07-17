<?php

namespace App\Template\ReadModel;

interface DocumentFetcherInterface
{
    public function getById(string $id): array;

    /**
     * @return list<array<string, mixed>>
     */
    public function getAllByCategory(string $categoryId): array;

    /**
     * @return array<string, mixed>
     */
    public function getBySlugAndCategoryId(string $slug, string $categoryId): array;
}
