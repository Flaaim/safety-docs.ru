<?php

namespace App\Template\ReadModel;

interface DocumentFetcherInterface
{
    public function getById(string $id): array;

    public function getPaginatedByCategory(string $categoryId, int $page = 1, int $limit = 15, ?string $search = null): array;
    /**
     * @return array<string, mixed>
     */
    public function getBySlugAndCategoryId(string $slug, string $categoryId): array;
}
