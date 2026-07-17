<?php

declare(strict_types=1);

namespace App\Template\Entity\Document;

use App\Template\Entity\Category\CategoryId;
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

    public function get(DocumentId $id): Document
    {
        $document = $this->repo->find($id);
        if (!$document) {
            throw new \RuntimeException('Document not found');
        }
        return $document;
    }

    public function findByCategoryIdAndName(CategoryId $categoryId, string $name): ?Document
    {
        return $this->repo->createQueryBuilder('d')
            ->join('d.category', 'c')
            ->where('c.categoryId = :categoryId')
            ->andWhere('d.name = :name')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
