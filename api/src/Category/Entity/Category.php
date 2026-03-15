<?php

namespace App\Category\Entity;

class Category
{
    public function __construct(
        private CategoryId $categoryId,
        private string $title,
        private string $description,
        private string $text,
        private Slug $slug,
        private string $directionId
    ){
    }
    public function getId(): CategoryId
    {
        return $this->categoryId;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getText(): string
    {
        return $this->text;
    }
    public function getSlug(): Slug
    {
        return $this->slug;
    }
    public function getDirectionId(): string
    {
        return $this->directionId;
    }
}