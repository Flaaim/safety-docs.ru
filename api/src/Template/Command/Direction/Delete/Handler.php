<?php

namespace App\Template\Command\Direction\Delete;

use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Direction\DirectionRepository;
use App\Flusher;

class Handler
{
    public function __construct(
        private DirectionRepository $directions,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $direction = $this->directions->findById(new DirectionId($command->directionId));
        if (null === $direction) {
            throw new \DomainException('Direction not found.');
        }

        if (!$direction->canBeDeleted()) {
            throw new \DomainException('Direction cannot be deleted.');
        }

        $this->directions->remove($direction);

        $this->flusher->flush();
    }
}
