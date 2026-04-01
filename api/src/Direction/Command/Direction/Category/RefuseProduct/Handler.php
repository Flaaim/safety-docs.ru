<?php

namespace App\Direction\Command\Direction\Category\RefuseProduct;

use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Flusher;

class Handler
{
    public function __construct(
        private CategoryRepository $categories,
        private Flusher $flusher
    ){
    }

    public function handle(Command $command): void
    {
        $category = $this->categories->findById(new CategoryId($command->categoryId));

        if(null === $category) {
            throw new \DomainException('Category not found.');
        }
        $category->refuseProduct();

        $this->flusher->flush();
    }
}