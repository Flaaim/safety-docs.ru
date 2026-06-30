<?php

namespace App\Template\Command\Direction\Category\Delete;

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
        if ($category === null) {
            throw new \DomainException('Category not found.');
        }

        if (!$category->canBeDeleted()) {
            throw new \DomainException('Category cannot be deleted. It has children.');
        }
        $category->release();

        $this->categories->remove($category);

        $this->flusher->flush();
    }
}
