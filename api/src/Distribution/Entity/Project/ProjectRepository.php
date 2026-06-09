<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Project;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class ProjectRepository
{
    private EntityRepository $repo;
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $repo = $em->getRepository(Project::class);
        $this->repo = $repo;
    }

    public function findById(ProjectId $id): ?Project
    {
        return $this->repo->find($id);
    }
}