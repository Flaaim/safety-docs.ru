<?php

namespace App\Product\Entity;

use App\Shared\Domain\ValueObject\Id;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

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
        if(!$product = $this->repo->find($id)) {
            throw new \DomainException('Product not found.');
        }
        /** @var Product $product */
        return $product;
    }
    public function findBySlug(Slug $slug): ?Product
    {
        return $this->repo->findOneBy(['slug' => $slug->getValue()]);
    }
    public function add(Product $product): void
    {
        $this->em->persist($product);
    }
    /** @return array<Product> */
    public function findAllPaginated(): array
    {
        return $this->repo->findAll();
    }
}