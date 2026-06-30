<?php

namespace App\Template\Command\Direction\Category\GetBySlug;

use App\Template\Entity\Category\CategoryRepository;
use App\Template\Entity\Category\DTO\CategoryDTO;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;

class Handler
{
    public function __construct(
        private readonly CategoryRepository $categories
    ) {
    }

    public function handle(Command $command): CategoryDTO
    {
        $slug = Slug::generate($command->slug);
        $directionId = new DirectionId($command->directionId);

        $category = $this->categories->findBySlug($slug, $directionId);

        if (null === $category) {
            throw new \DomainException('Category not found.');
        }

        return CategoryDTO::fromCategory($category);
    }
}
