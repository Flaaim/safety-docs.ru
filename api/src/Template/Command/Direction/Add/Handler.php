<?php

namespace App\Template\Command\Direction\Add;

use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Direction\DirectionRepository;
use App\Template\Entity\Slug;
use App\Flusher;

class Handler
{
    public function __construct(
        private readonly DirectionRepository $directions,
        private readonly Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $slug = Slug::generate($command->title);
        $direction = $this->directions->findBySlug($slug);
        if ($direction) {
            throw new \DomainException("Direction with slug " . $slug->getValue() . " is exists");
        }
        $direction = new Direction(
            DirectionId::generate(),
            $command->title,
            $command->description,
            $command->text,
            $slug
        );
        $this->directions->add($direction);

        $this->flusher->flush();
    }
}
