<?php

namespace App\Direction\Command\Direction\GetAll;

use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Direction\DTO\DirectionsCollection;

class Handler
{
    public function __construct(
        private readonly DirectionRepository $directions
    ){
    }
    public function handle(): DirectionsCollection
    {
        $directions = $this->directions->getAll();

        return new DirectionsCollection($directions, count($directions));
    }
}