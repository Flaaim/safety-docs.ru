<?php

namespace App\Product\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ProductRepository
{
    private EntityRepository $repo;
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Product::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function get(ProductId $id): Product
    {
        if (!$product = $this->repo->find($id)) {
            throw new \DomainException('Product not found.');
        }
        /** @var Product $product */
        return $product;
    }
    public function add(Product $product): void
    {
        $this->em->persist($product);
    }
    /** @return array{items: Product[], total: int} */
    public function findPaginated(int $page, int $perPage, string $sortBy = 'updatedAt', string $sortDir = 'DESC'): array
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

    public function findById(ProductId $id): ?Product
    {
        return $this->repo->find($id);
    }
}
