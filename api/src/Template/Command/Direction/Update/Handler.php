<?php

namespace App\Template\Command\Direction\Update;

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
        $directionId = new DirectionId($command->directionId);
        $direction = $this->directions->findById($directionId);

        if (null === $direction) {
            throw new \DomainException('Direction not found.');
        }

        $existingDirection = $this->directions->findBySlug($slug);

        if ($existingDirection && !$existingDirection->getId()->equals($directionId)) {
            throw new \DomainException('Direction with this slug already exists.');
        }

        $direction->update(
            $command->title,
            $command->description,
            $command->text,
            $slug->getValue()
        );

        $this->flusher->flush();
    }
}
