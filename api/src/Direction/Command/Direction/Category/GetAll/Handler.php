<?php

namespace App\Direction\Command\Direction\Category\GetAll;

use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Category\DTO\CategoriesCollection;
use App\Direction\Entity\Direction\DirectionId;

class Handler
{
    public function __construct(
        private readonly CategoryRepository $categories,
    ){
    }

    public function handle(Command $command): CategoriesCollection
    {
        $categories = $this->categories->findByDirectionId(new DirectionId($command->directionId));

        return new CategoriesCollection(
            $categories,
            count($categories)
        );
    }
}