<?php

namespace App\Template\Command\Direction\GetBySlug;

use App\Template\Entity\Category\DTO\CategoryDTOMapper;
use App\Template\Entity\Direction\DirectionRepository;
use App\Template\Entity\Direction\DTO\DirectionDTO;
use App\Template\Entity\Slug;

class Handler
{
    public function __construct(
        private readonly DirectionRepository $directions,
        private readonly CategoryDTOMapper $categoryDTOMapper,
    ) {
    }
    public function handle(Command $command): DirectionDTO
    {
        $direction = $this->directions->findBySlug(Slug::generate($command->slug));
        if (null === $direction) {
            throw new \DomainException('Direction not found.');
        }

        return new DirectionDTO(
            $direction->getId()->getValue(),
            $direction->getTitle(),
            $direction->getDescription(),
            $direction->getText(),
            $direction->getSlug()->getValue(),
            $this->categoryDTOMapper->mapCollection($direction->getCategories()->toArray())
        );
    }
}
