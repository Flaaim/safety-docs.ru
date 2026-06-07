<?php

declare(strict_types=1);

namespace App\Distribution\Entity\File;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

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
}
