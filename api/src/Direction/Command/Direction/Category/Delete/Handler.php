<?php

namespace App\Direction\Command\Direction\Category\Delete;

use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Flusher;
use Symfony\Component\Console\Exception\CommandNotFoundException;

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
        if($category === null) {
            throw new \DomainException('Category not found.');
        }

        if(!$category->canBeDeleted()) {
            throw new \DomainException('Category cannot be deleted. It has children.');
        }

        $this->categories->remove($category);
        $this->flusher->flush();
    }
}