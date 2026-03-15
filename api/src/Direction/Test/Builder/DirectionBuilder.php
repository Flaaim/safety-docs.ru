<?php

namespace App\Direction\Test\Builder;

use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class DirectionBuilder
{
    private DirectionId $id;
    private Slug $slug;
    private string $title;
    private string $description;
    private string $text;
    private Collection $categories;
    public function __construct()
    {
        $this->id = new DirectionId('2a7a593a-ee23-4a73-bb07-b372438fb269');
        $this->slug = new Slug('safety');
        $this->title = 'Охрана труда';
        $this->description = 'Описание направления охрана труда';
        $this->text = 'Текст к направлению темы охрана труда';
        $this->categories = new ArrayCollection();
    }

    public function withId(DirectionId $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function withSlug(Slug $slug): self
    {
        $this->slug = $slug;
        return $this;
    }
    public function withTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    public function withDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
    public function withText(string $text): self
    {
        $this->text = $text;
        return $this;
    }
    public function withCategories(Collection $categories): self
    {
        $this->categories = $categories;
        return $this;
    }
    public function build(): Direction
    {
        return new Direction(
            $this->id,
            $this->title,
            $this->description,
            $this->text,
            $this->slug
        );
    }
}