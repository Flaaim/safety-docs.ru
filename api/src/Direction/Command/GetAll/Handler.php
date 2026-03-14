<?php

namespace App\Direction\Command\GetAll;

use App\Direction\Entity\DirectionRepository;
use App\Direction\Entity\DTO\DirectionsCollection;

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