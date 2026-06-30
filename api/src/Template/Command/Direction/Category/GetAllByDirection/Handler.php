<?php

namespace App\Template\Command\Direction\Category\GetAllByDirection;

use App\Template\Entity\Category\CategoryRepository;
use App\Template\Entity\Category\DTO\CategoriesCollection;
use App\Template\Entity\Category\DTO\CategoryDTOMapper;
use App\Template\Entity\Direction\DirectionId;

class Handler
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly CategoryDTOMapper $categoryDTOMapper,
    ) {
    }

    public function handle(Command $command): CategoriesCollection
    {
        $categories = $this->categories->findByDirectionId(new DirectionId($command->directionId));
        if (empty($categories)) {
            throw new \DomainException('Categories not found.');
        }

        $categoriesDTO = $this->categoryDTOMapper->mapCollection($categories);

        return new CategoriesCollection(
            $categoriesDTO,
            count($categoriesDTO)
        );
    }
}
