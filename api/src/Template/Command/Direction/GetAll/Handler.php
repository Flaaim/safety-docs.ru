<?php

namespace App\Template\Command\Direction\GetAll;

use App\Template\Entity\Direction\DirectionRepository;
use App\Template\Entity\Direction\DTO\DirectionsCollection;

class Handler
{
    public function __construct(
        private readonly DirectionRepository $directions
    ) {
    }
    public function handle(): DirectionsCollection
    {
        $directions = $this->directions->getAll();

        return new DirectionsCollection($directions, count($directions));
    }
}
