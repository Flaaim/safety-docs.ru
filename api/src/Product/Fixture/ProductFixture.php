<?php

namespace App\Product\Fixture;

use App\Product\Entity\File;
use App\Product\Entity\Amount;
use App\Product\Entity\Product;
use App\Product\Entity\ProductId;
use App\Shared\Domain\ValueObject\Currency;
use App\Shared\Domain\ValueObject\UpdatedAt;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $product = new Product(
            new ProductId('e2ff37fb-8690-46e5-82fa-75b5ceca8b61'),
            'Система управления охраной труда',
            new Amount(550.00, new Currency('RUB')),
            new File('/safety/suot/suot200.1'),
            'suot200.1',
            'suot',
            new \DateTimeImmutable('now'),
        );

        $manager->persist($product);

        $product2 = new Product(
            new ProductId('63beafba-d948-4ddf-9463-54da074ae903'),
            'Комплект документов по обучению требованиям охраны труда',
            new Amount(750.00, new Currency('RUB')),
            new File('/edu/templates.txt'),
            'edu2026.1',
            'edu2026',
            new \DateTimeImmutable(),
        );

        $manager->persist($product2);

        $product3 = new Product(
            new ProductId('658f2bb4-14e5-472e-a543-e3091c231eee'),
            'Служба охраны труда',
            new Amount(250.00, new Currency('RUB')),
            new File('/safety/service/serv100.1.rar'),
            'serv100.1',
            'service',
            new \DateTimeImmutable(),
        );

        $manager->persist($product3);

        $manager->flush();
    }
}