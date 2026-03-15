<?php

namespace App\Category\Command\GetAll;

use App\Category\Entity\CategoryRepository;
use App\Category\Entity\DTO\CategoriesCollection;

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