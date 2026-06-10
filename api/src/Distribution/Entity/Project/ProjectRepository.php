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
    public function hasByName(string $name): bool
    {
        $project = $this->repo->findOneBy(['name' => $name]);
        if (null === $project) {
            return false;
        }
        return true;
    }
    public function hasById(ProjectId $id): bool
    {
        $project = $this->repo->findOneBy(['id' => $id]);
        if (null === $project) {
            return false;
        }
        return true;
    }
    public function findById(ProjectId $id): ?Project
    {
        return $this->repo->find($id);
    }
    public function add(Project $project): void
    {
        $this->em->persist($project);
    }
    /** @return array<Project> */
    public function findAll(): array
    {
        return $this->repo->findAll();
    }
}
