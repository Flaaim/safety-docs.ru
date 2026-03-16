<?php

namespace App\Direction\Command\Direction\Update;

use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Slug;
use App\Flusher;

class Handler
{
    public function __construct(
        private readonly DirectionRepository $directions,
        private readonly Flusher $flusher
    ){
    }
    public function handle(Command $command): void
    {
        $slug = new Slug($command->slug);
        $directionId = new DirectionId($command->directionId);
        $direction = $this->directions->findById($directionId);

        if(null === $direction) {
            throw new \DomainException('Direction not found.');
        }

        $existingDirection = $this->directions->findBySlug($slug);

        if($existingDirection) {
            if (!$existingDirection->getId()->equals($directionId)) {
                throw new \DomainException('Direction with this slug already exists.');
            }
        }


        $direction->update(
            $command->title,
            $command->description,
            $command->text,
            $slug
        );

        $this->flusher->flush();
    }
}