<?php

namespace App\Direction\Entity\Category\DTO;

use App\Direction\Entity\Category\Category;

class CategoryDTOMapper
{
    public function map(Category $category): CategoryDTO
    {
        return new CategoryDTO(
            $category->getId(),
            $category->getTitle(),
            $category->getDescription(),
            $category->getText(),
            $category->getSlug()->getValue(),
            $category->getDirection()->getId()->getValue(),
            $category->getProduct()->getId()->getValue(),
        );
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