<?php

namespace App\Direction\Entity\Category\DTO;

use App\Direction\Entity\Category\Category;

class CategoryDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $text,
        public string $slug,
        public string $directionId,
        public ?string $productId = null,
    ){
    }

    public static function fromCategory(Category $category): self
    {
        $product = $category->getProduct();
        if($product !== null) {
            $productId = $product->getId();
        }
        return new CategoryDTO(
            $category->getId(),
            $category->getTitle(),
            $category->getDescription(),
            $category->getText(),
            $category->getSlug()->getValue(),
            $category->getDirection()->getId()->getValue(),
            $productId ?? null,
        );
    }
}