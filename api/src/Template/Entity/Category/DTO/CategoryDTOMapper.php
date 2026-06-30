<?php

namespace App\Template\Entity\Category\DTO;

use App\Template\Entity\Category\Category;

class CategoryDTOMapper
{
    public function map(Category $category): CategoryDTO
    {
        return CategoryDTO::fromCategory($category);
    }

    public function mapCollection(array $categories): array
    {
        $result = [];
        foreach ($categories as $category) {
            $result[] = $this->map($category);
        }
        return $result;
    }
}
