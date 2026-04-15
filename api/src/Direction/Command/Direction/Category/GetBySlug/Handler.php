<?php

namespace App\Direction\Command\Direction\Category\GetBySlug;

use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Category\DTO\CategoryDTO;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;

class Handler
{
    public function __construct(
        private readonly CategoryRepository $categories
    ){
    }

    public function handle(Command $command): CategoryDTO
    {
        $slug = new Slug($command->slug);
        $directionId = new DirectionId($command->directionId);

        $category = $this->categories->findBySlug($slug, $directionId);

        if(null === $category){
            throw new \DomainException('Category not found.');
        }

        return CategoryDTO::fromCategory($category);
    }
}