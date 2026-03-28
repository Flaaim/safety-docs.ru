<?php

namespace App\Direction\Entity\Category;

use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Slug;
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
        #[ORM\ManyToOne(targetEntity: Direction::class, inversedBy: 'categories')]
        #[ORM\JoinColumn(name: 'direction_id', referencedColumnName: 'id', nullable: false, onDelete: "RESTRICT")]
        private Direction $direction
    ){
        $direction->addCategory($this);
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
    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function update(string $title, string $description, string $text, SLug $slug, Direction $direction): void
    {
        $this->title = $title;
        $this->description = $description;
        $this->text = $text;
        $this->slug = $slug;
        $this->direction = $direction;
    }
}