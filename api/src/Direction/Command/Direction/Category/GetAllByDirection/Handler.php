<?php

namespace App\Direction\Command\Direction\Category\GetAllByDirection;

use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Category\DTO\CategoriesCollection;
use App\Direction\Entity\Category\DTO\CategoryDTOMapper;
use App\Direction\Entity\Direction\DirectionId;

class Handler
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly CategoryDTOMapper $categoryDTOMapper,
    ){
    }

    public function handle(Command $command): CategoriesCollection
    {
        $categories = $this->categories->findByDirectionId(new DirectionId($command->directionId));
        if(empty($categories)) {
            throw new \DomainException('Categories not found.');
        }

        $categoriesDTO = $this->categoryDTOMapper->mapCollection($categories);

        return new CategoriesCollection(
            $categoriesDTO,
            count($categoriesDTO)
        );
    }
}