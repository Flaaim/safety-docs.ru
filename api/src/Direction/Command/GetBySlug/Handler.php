<?php

namespace App\Direction\Command\GetBySlug;

use App\Direction\Entity\DirectionRepository;
use App\Direction\Entity\DTO\DirectionDTO;

class Handler
{
    public function __construct(
      private readonly DirectionRepository $directions
    ){
    }
    public function handle(Command $command): DirectionDTO
    {
        $direction = $this->directions->getBySlug($command->slug);

        return new DirectionDTO(
            $direction->getSlug()->getValue(),
            $direction->getTitle(),
            $direction->getDescription(),
            $direction->getText()
        );
    }
}