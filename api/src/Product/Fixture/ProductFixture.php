<?php

namespace App\Product\Fixture;

use App\Product\Entity\Currency;
use App\Product\Entity\File;
use App\Product\Entity\Price;
use App\Product\Entity\Product;
use App\Product\Entity\ProductId;
use App\Shared\Domain\ValueObject\Id;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $product = new Product(
            new ProductId('e2ff37fb-8690-46e5-82fa-75b5ceca8b61'),
            'Полный комплект ЛНА по охране труда',
            new Price(2550.00, new Currency('RUB')),
            new File('/lna/templates.txt'),
            'lna2026.1',
            'lna2026'
        );

        $manager->persist($product);

        $product2 = new Product(
            new ProductId('63beafba-d948-4ddf-9463-54da074ae903'),
            'Комплект документов по обучению требованиям охраны труда',
            new Price(750.00, new Currency('RUB')),
            new File('/edu/templates.txt'),
            'edu2026.1',
            'edu2026'
        );

        $manager->persist($product2);

        $product3 = new Product(
            new ProductId('658f2bb4-14e5-472e-a543-e3091c231eee'),
            'Комплект документов служба ОТ и системы управления охраной труда',
            new Price(550.00, new Currency('RUB')),
            new File('/syot/templates.txt'),
            'syot026.1',
            'syot2026'
        );

        $manager->persist($product3);

        $manager->flush();
    }
}