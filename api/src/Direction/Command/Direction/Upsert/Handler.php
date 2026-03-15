<?php

namespace App\Direction\Command\Direction\Upsert;

use App\Direction\Entity\Direction\Direction;
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

        if(null === $direction){
            if($this->directions->existsBySlug($slug)){
                throw new \DomainException('Direction slug already exists.');
            }
            $direction = new Direction(
                $directionId,
                $command->title,
                $command->description,
                $command->text,
                $slug
            );
            $this->directions->add($direction);
        }else{
            if (!$direction->getSlug()->equals($slug)) {
                if ($this->directions->existsBySlug($slug)) {
                    throw new \DomainException('New slug is already taken by another direction.');
                }
            }

            $direction->update(
                $command->title,
                $command->description,
                $command->text,
                $slug
            );
        }

        $this->flusher->flush();
    }
}