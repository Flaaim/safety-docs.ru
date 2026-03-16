<?php

namespace App\Direction\Command\Direction\Add;

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
        $direction = $this->directions->findBySlug($slug);
        if($direction){
            throw new \DomainException("Direction with slug ".$command->slug." is exists");
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