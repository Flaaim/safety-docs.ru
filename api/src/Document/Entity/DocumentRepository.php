<?php

declare(strict_types=1);

namespace App\Document\Entity;

use App\Documents\Entity\Document;
use App\Documents\Entity\DocumentId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DocumentRepository
{
    private EntityRepository $repo;
    public function __construct(
       private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Document::class);

    }

    public function findById(DocumentId $id): ?Document
    {
        return $this->repo->find($id);
    }

    public function add(Document $document): void
    {
        $this->em->persist($document);
    }
}