<?php

namespace App\Product\Fixture;

use App\Product\Entity\Amount;
use App\Product\Entity\Filename;
use App\Product\Entity\FormatDocument;
use App\Product\Entity\Product;
use App\Product\Entity\ProductId;
use App\Product\Entity\Slug;
use App\Shared\Domain\ValueObject\Currency;
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
            new Filename('suot200.5.rar'),
            'suot200.1',
            new Slug('suot'),
            new \DateTimeImmutable('now'),
            10,
            [FormatDocument::PDF, FormatDocument::DOCX],
        );

        $manager->persist($product);

        $product2 = new Product(
            new ProductId('63beafba-d948-4ddf-9463-54da074ae903'),
            'Комплект документов по обучению требованиям охраны труда',
            new Amount(750.00, new Currency('RUB')),
            new Filename('template100.2.rar'),
            'edu2026.1',
            new Slug('edu2026'),
            new \DateTimeImmutable(),
            12,
            [FormatDocument::PDF, FormatDocument::DOCX],
        );

        $manager->persist($product2);

        $product3 = new Product(
            new ProductId('658f2bb4-14e5-472e-a543-e3091c231eee'),
            'Служба охраны труда',
            new Amount(250.00, new Currency('RUB')),
            new Filename('serv100.1.rar'),
            'serv100.1',
            new Slug('service'),
            new \DateTimeImmutable(),
            40,
            [FormatDocument::PDF, FormatDocument::DOCX],
        );

        $manager->persist($product3);

        $manager->flush();
    }
}