<?php

namespace App\Direction\Entity\Category\DTO\Admin;

class CategoryPaginatedDTOMapper
{
    public function map(
       array $categories,
       int $page,
       int $perPage
    ): CategoryPaginatedDTO
    {
        $total = count($categories);
        $offset = ($page - 1) * $perPage;

        $slicedCategories = array_slice($categories, $offset, $perPage);

        return new CategoryPaginatedDTO(
            $slicedCategories,
            $total,
            $page,
            $perPage,
            (int) ceil($total / $perPage) ?: 1
        );
    }
}