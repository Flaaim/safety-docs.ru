<?php

namespace App\Direction\Command\Direction\Category\GetBySlug;

use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Category\DTO\CategoryDTO;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use App\Flusher;

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

        return new CategoryDTO(
            $category->getId()->getValue(),
            $category->getTitle(),
            $category->getDescription(),
            $category->getText(),
            $category->getSlug()->getValue(),
            $category->getDirection()->getId()->getValue(),
        );
    }
}