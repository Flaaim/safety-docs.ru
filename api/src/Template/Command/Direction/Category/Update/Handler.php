<?php

namespace App\Template\Command\Direction\Category\Update;

use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Category\CategoryRepository;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Direction\DirectionRepository;
use App\Template\Entity\Slug;
use App\Flusher;

class Handler
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly DirectionRepository $directions,
        private readonly Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $slug = Slug::generate($command->title);
        $directionId = new DirectionId($command->directionId);

        $direction = $this->directions->findById($directionId);

        if ($direction === null) {
            throw new \DomainException('Direction not found.');
        }

        $category = $this->categories->findById(new CategoryId($command->categoryId));

        if ($category === null) {
            throw new \DomainException('Category not found.');
        }

        $existingCategory = $this->categories->findBySlug($slug, $directionId);

        if ($existingCategory && !$existingCategory->getId()->equals($category->getId())) {
            throw new \DomainException('Category with slug ' . $slug->getValue() . ' is exists.');
        }

        $parentCategory = null;

        if ($command->parentId !== null) {
            $parentCategory = $this->categories->findById(new CategoryId($command->parentId));

            if ($parentCategory === null) {
                throw new \DomainException('Parent category not found.');
            }
        }

        $category->update(
            $command->title,
            $command->description,
            $command->text,
            $slug->getValue(),
        );

        if (!$category->getDirection()->getId()->equals($directionId)) {
            $category->updateDirection($direction);
        }

        $currentParent = $category->getParent();

        if ($command->parentId === null) {
            if ($currentParent !== null) {
                $category->makeRoot();
            }
        } elseif (
            $parentCategory !== null
            && ($currentParent === null || !$currentParent->getId()->equals($parentCategory->getId()))
        ) {
            $category->changeParent($parentCategory);
        }

        $this->flusher->flush();
    }
}
