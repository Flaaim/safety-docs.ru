<?php

namespace App\Direction\Command\Direction\Delete;

use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Flusher;

class Handler
{
    public function __construct(
        private DirectionRepository $directions,
        private Flusher $flusher
    ){
    }

    public function handle(Command $command): void
    {
        $direction = $this->directions->findById(new DirectionId($command->directionId));
        if(null === $direction) {
            throw new \DomainException('Direction not found.');
        }

        if(!$direction->canBeDeleted()) {
            throw new \DomainException('Direction cannot be deleted.');
        }

        $this->directions->remove($direction);

        $this->flusher->flush();
    }
}