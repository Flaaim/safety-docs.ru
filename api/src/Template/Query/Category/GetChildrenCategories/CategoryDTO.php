<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetChildrenCategories;

final class CategoryDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $text,
        public string $slug
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['category_id'],
            title: $data['title'],
            description: $data['description'],
            text: $data['text'],
            slug: $data['slug']
        );
    }
}
