<?php

namespace App\Template\Command\Direction\Category\Add;

use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Category\CategoryRepository;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Direction\DirectionRepository;
use App\Template\Entity\Slug;
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
            Slug::generate($command->title)->getValue(),
            $direction,
            $parentCategory
        );

        $this->flusher->flush();
    }
}
