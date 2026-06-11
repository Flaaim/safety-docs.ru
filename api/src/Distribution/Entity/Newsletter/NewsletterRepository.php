<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class NewsletterRepository
{
    private EntityRepository $repo;
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $repo = $em->getRepository(Newsletter::class);
        $this->repo = $repo;
    }

    public function add(Newsletter $newsletter): void
    {
        $this->em->persist($newsletter);
    }
    /** @return array<Newsletter> */
    public function findAll(): array
    {
        return $this->repo->findAll();
    }
}