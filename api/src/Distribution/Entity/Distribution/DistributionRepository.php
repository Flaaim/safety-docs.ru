<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Distribution;

use App\Distribution\Entity\Distrubution;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DistributionRepository
{
    private EntityRepository $repo;
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $repo = $em->getRepository(Distrubution::class);
        $this->repo = $repo;
    }

    public function findById(DistributionId $id): ?Distrubution
    {
        return $this->repo->find($id);
    }
}