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
        public string $directionTitle,
        public array $children,
        public ?string $productId = null,
        public ?string $productTitle = null,
        public ?string $parentId = null,
    ){
    }

    public static function fromCategory(Category $category): self
    {
        $product = $category->getProduct();
        $productId = null;
        $productTitle = null;

        if($product !== null) {
            $productId = $product->getId()->getValue();
            $productTitle = $product->getName();
        }
        $parentId = null;
        if($category->getParent() !== null) {
            $parentId = $category->getParent()->getId()->getValue();
        }

        $childrenDTOs = array_map(
            fn(Category $child) => self::fromCategory($child),
            $category->getChildren()
        );

        return new CategoryDTO(
            $category->getId(),
            $category->getTitle(),
            $category->getDescription(),
            $category->getText(),
            $category->getSlug()->getValue(),
            $category->getDirection()->getId()->getValue(),
            $category->getDirection()->getTitle(),
            $childrenDTOs,
            $productId,
            $productTitle,
            $parentId,
        );
    }
}