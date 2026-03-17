<?php

namespace App\Direction\Command\Direction\GetBySlug;

use App\Direction\Entity\Category\DTO\CategoryDTOMapper;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Direction\DTO\DirectionDTO;
use App\Direction\Entity\Slug;

class Handler
{
    public function __construct(
        private readonly DirectionRepository $directions,
        private readonly CategoryDTOMapper $categoryDTOMapper,
    ){
    }
    public function handle(Command $command): DirectionDTO
    {
        $direction = $this->directions->findBySlug(new Slug($command->slug));
        if(null === $direction){
            throw new \DomainException('Direction not found.');
        }

        return new DirectionDTO(
            $direction->getTitle(),
            $direction->getDescription(),
            $direction->getText(),
            $direction->getSlug()->getValue(),
            $this->categoryDTOMapper->mapCollection($direction->getCategories()->toArray())
        );
    }
}