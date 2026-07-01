<?php

declare(strict_types=1);

namespace App\Template\Query\Category\GetBySlug;

final class CategoryDTO
{
    /** @param CategoryDTO[] $children */
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $text,
        public string $slug,
        public ?string $parentId,
        public string $directionId,
        public array $children
    ) {}

    public static function fromArray(array $data): self
    {
        $children = array_map(function (array $child) {
            return self::fromArray($child);
        }, $data['children'] ?? []);

        return new self(
            id: $data['category_id'],
            title: $data['title'],
            description: $data['description'],
            text: $data['text'],
            slug: $data['slug'],
            parentId: $data['parent_id'] ?? null,
            directionId: $data['direction_id'],
            children: $children
        );
    }
}