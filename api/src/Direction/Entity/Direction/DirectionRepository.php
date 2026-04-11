<?php

namespace App\Direction\Entity\Direction;

use App\Direction\Entity\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DirectionRepository
{
    private EntityRepository $repo;
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Direction::class);
        $this->repo = $repo;
        $this->em = $em;
    }
    public function findById(DirectionId $id): ?Direction
    {
        return $this->repo->find($id);
    }

    public function findBySlug(Slug $slug): ?Direction
    {
        return $this->repo->findOneBy(['slug' => $slug]);
    }

    /** @return array<Direction> */
    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    public function add(Direction $direction): void
    {
        $this->em->persist($direction);
    }

    public function remove(Direction $direction): void
    {
        $this->em->remove($direction);
    }
}