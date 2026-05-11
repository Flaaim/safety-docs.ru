<?php

namespace App\Direction\Command\Direction\Category\Add;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Direction\DirectionRepository;
use App\Direction\Entity\Slug;
use App\Flusher;

class Handler
{
    public function __construct(
        private readonly DirectionRepository $directions,
        private readonly CategoryRepository $categories,
        private readonly Flusher $flusher
    ) {
    }
    public function handle(Command $command): void
    {
        $direction = $this->directions->findById(new DirectionId($command->directionId));
        if (null === $direction) {
            throw new \DomainException('Direction not found.');
        }
        $parentCategory = null;

        if ($command->parentId !== null) {
            $parentCategory = $this->categories->findById(new CategoryId($command->parentId));

            if (null === $parentCategory) {
                throw new \DomainException('Parent category not found.');
            }
        }

        new Category(
            CategoryId::generate(),
            $command->title,
            $command->description,
            $command->text,
            Slug::generate($command->title),
            $direction,
            $parentCategory
        );

        $this->flusher->flush();
    }
}
