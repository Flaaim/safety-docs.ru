<?php

namespace App\Template\Command\Direction\Category\RefuseProduct;

use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Category\CategoryRepository;
use App\Flusher;

class Handler
{
    public function __construct(
        private CategoryRepository $categories,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $category = $this->categories->findById(new CategoryId($command->categoryId));

        if (null === $category) {
            throw new \DomainException('Category not found.');
        }
        $category->refuseProduct();

        $this->flusher->flush();
    }
}
