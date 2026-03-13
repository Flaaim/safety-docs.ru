<?php

namespace App\Direction\Entity;

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
    public function findBySlug(string $slug): ?Direction
    {
        return $this->repo->findOneBy(['slug' => $slug]);
    }
}