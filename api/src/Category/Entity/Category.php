<?php

namespace App\Category\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'category_id', unique: true)]
        private CategoryId $categoryId,
        #[ORM\Column(type: 'string', length: 150)]
        private string $title,
        #[ORM\Column(type: 'string', length: 255)]
        private string $description,
        #[ORM\Column(type: 'text')]
        private string $text,
        #[ORM\Column(type: 'category_slug', length: 35)]
        private Slug $slug,
        #[ORM\Column(type: 'string', unique: true)]
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