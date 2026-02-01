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

    public function get(Id $id): Product
    {
        if(!$product = $this->repo->find($id)) {
            throw new \DomainException('Product not found.');
        }
        /** @var Product $product */
        return $product;
    }
    public function findByCourse(string $course): ?Product
    {
        return $this->repo->findOneBy(['course' => $course]);
    }
    public function upsert(Product $product): void
    {
        $qb = $this->em->createQueryBuilder();

        $sql = '
        INSERT INTO products (id, name, price, file, cipher, course) 
        VALUES (:id, :name, :price, :file, :cipher, :course)
        ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            price = VALUES(price),
            file = VALUES(file), 
            cipher = VALUES(cipher)
         ';

        $qb->getEntityManager()
            ->getConnection()
            ->executeStatement($sql, [
                'id' => $product->getId()->getValue(),
                'name' => $product->getName(),
                'price' => $product->getPrice()->getValue(),
                'file' => $product->getFile()->getPathToFile(),
                'cipher' => $product->getCipher(),
                'course' => $product->getCourse()
            ]);
    }
}