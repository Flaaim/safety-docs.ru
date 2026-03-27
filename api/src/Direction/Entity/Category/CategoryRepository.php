<?php

namespace App\Direction\Entity\Category;

use App\Direction\Entity\Category\DTO\CategoryDTO;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CategoryRepository
{
    private EntityRepository $repo;
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Category::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function findById(CategoryId $id): ?Category
    {
        return $this->repo->find($id);
    }
    /** @return array<Category> */
    public function findByDirectionId(DirectionId $directionId): array
    {
        return $this->repo->findBy(['direction' => $directionId]);
    }

    /**
     * @return array<Category>
     */
    public function getAllPaginated(): array
    {
        return $this->repo->findAll();
    }
    public function findBySlug(Slug $slug, DirectionId $directionId): ?Category
    {
        return $this->repo->findOneBy([
            'slug' => $slug->getValue(),
            'direction' => $directionId->getValue()
        ]);
    }
}