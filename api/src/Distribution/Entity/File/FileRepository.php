<?php

declare(strict_types=1);

namespace App\Distribution\Entity\File;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class FileRepository
{
    private EntityRepository $repo;
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $repo = $em->getRepository(File::class);
        $this->repo = $repo;
    }
    public function add(File $file): void
    {
        $this->em->persist($file);
    }
    public function findById(FileId $id): ?File
    {
        return $this->repo->find($id);
    }
    public function remove(File $file): void
    {
        $this->em->remove($file);
    }
    public function findByName(string $name): ?File
    {
        return $this->repo->findOneBy(['name' => $name]);
    }
    public function findPaginated(int $page, int $perPage, string $sortBy = 'date', string $sortDir = 'DESC'): array
    {
        $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

        $qb = $this->repo->createQueryBuilder('p')
            ->orderBy('p.' . $sortBy, $sortDir)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb);

        return [
            'items' => iterator_to_array($paginator),
            'total' => count($paginator),
        ];
    }
}
