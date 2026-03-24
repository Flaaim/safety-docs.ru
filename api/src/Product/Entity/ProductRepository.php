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
    public function upsert(Product $product): void
    {
        $qb = $this->em->createQueryBuilder();

        $sql = '
        INSERT INTO products (id, name, amount, file, cipher, slug) 
        VALUES (:id, :name, :amount, :file, :cipher, :slug)
        ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            amount = VALUES(amount),
            file = VALUES(file), 
            slug = VALUES(slug)
         ';

        $qb->getEntityManager()
            ->getConnection()
            ->executeStatement($sql, [
                'id' => $product->getId()->getValue(),
                'name' => $product->getName(),
                'amount' => $product->getAmount()->getValue(),
                'file' => $product->getFile()->getValue(),
                'cipher' => $product->getCipher(),
                'slug' => $product->getSlug()
            ]);
    }
    /** @return array<Product> */
    public function findAllPaginated(): array
    {
        return $this->repo->findAll();
    }
}