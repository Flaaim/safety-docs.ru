<?php

namespace App\Direction\Command\Direction\Category\Update;

use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Slug;
use App\Flusher;

class Handler
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly DirectionRepository $directions,
        private readonly Flusher $flusher
    ){}

    public function handle(Command $command): void
    {
        $slug = new Slug($command->slug);
        $directionId = new DirectionId($command->directionId);

        $direction = $this->directions->findById($directionId);

        if($direction === null) {
            throw new \DomainException('Direction not found.');
        }

        $category = $this->categories->findById(new CategoryId($command->categoryId));

        if($category === null) {
            throw new \DomainException('Category not found.');
        }

        $existingCategory = $this->categories->findBySlug($slug, $directionId);

        if($existingCategory && !$existingCategory->getId()->equals($category->getId())) {
            throw new \DomainException('Category with slug '. $slug->getValue() .' is exists.');
        }

        $category->update(
            $command->title,
            $command->description,
            $command->text,
            $slug,
        );

        $this->flusher->flush();

    }
}