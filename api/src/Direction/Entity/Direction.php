<?php

namespace App\Direction\Entity;


use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: 'directions')]
class Direction
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'direction_id', unique: true)]
        private DirectionId $id,
        #[ORM\Column(type: 'string', length: 150)]
        private string $title,
        #[ORM\Column(type: 'string', length: 255)]
        private string $description,
        #[ORM\Column(type: 'text')]
        private string $text,
        #[ORM\Column(type: 'direction_slug', length: 35)]
        private Slug $slug
    ){
    }
    public function getId(): DirectionId
    {
        return $this->id;
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

    public function update(string $title, string $description, string $text, Slug $slug): void
    {
        $this->title = $title;
        $this->description = $description;
        $this->text = $text;
        $this->slug = $slug;
    }

}