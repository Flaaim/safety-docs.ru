<?php

namespace App\Category\Entity;

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
    public function findByDirectionId(string $directionId): array
    {
        $qb = $this->repo->createQueryBuilder('c');
        $qb->where('c.directionId = :directionId');
        $qb->setParameter('directionId', $directionId);
        return $qb->getQuery()->getResult();
    }
}