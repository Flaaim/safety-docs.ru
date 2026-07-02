<?php

namespace App\Template\Test\Builder;

use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;

class DirectionBuilder
{
    private DirectionId $id;
    private string $slug;
    private string $title;
    private string $description;
    private string $text;
    public function __construct()
    {
        $this->id = new DirectionId('2a7a593a-ee23-4a73-bb07-b372438fb269');
        $this->slug = Slug::generate('Охрана труда')->getValue();
        $this->title = 'Охрана труда';
        $this->description = 'Описание направления охрана труда';
        $this->text = 'Текст к направлению темы охрана труда';
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withId(DirectionId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withTitle(string $title): self
    {
        $clone = clone $this;
        $clone->title = $title;
        $clone->slug = Slug::generate($title)->getValue();
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withDescription(string $description): self
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withText(string $text): self
    {
        $clone = clone $this;
        $clone->text = $text;
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
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
