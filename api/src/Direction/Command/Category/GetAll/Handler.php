<?php

namespace App\Direction\Command\Category\GetAll;

use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Category\DTO\CategoriesCollection;

class Handler
{
    public function __construct(
        private readonly CategoryRepository $categories,
    ){
    }

    public function handle(Command $command): CategoriesCollection
    {
        $categories = $this->categories->findByDirectionId($command->directionId);

        return new CategoriesCollection(
            $categories,
            count($categories)
        );
    }
}