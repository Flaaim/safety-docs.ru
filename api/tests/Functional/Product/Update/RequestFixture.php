<?php

namespace Test\Functional\Product\Update;

use App\Product\Entity\File;
use App\Product\Entity\Amount;
use App\Product\Entity\Filename;
use App\Product\Entity\ProductId;
use App\Product\Entity\Slug;
use App\Product\Test\ProductBuilder;
use App\Shared\Domain\ValueObject\Currency;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $product = (new ProductBuilder())
            ->withId(new ProductId('b38e76c0-ac23-4c48-85fd-975f32c8801f'))
            ->withName('Служба охраны труда')
            ->withCipher('serv100.1')
            ->withPrice(new Amount(550.00, new Currency('RUB')))
            ->withFilename(new Filename('serv100.1.rar'))
            ->withSlug(new Slug('service'))
            ->withUpdatedAt(new \DateTimeImmutable())
            ->build();

        $anotherProduct = (new ProductBuilder())
            ->withId(new ProductId('df455720-90b9-4624-8d36-35b16e2716ac'))
            ->withName('Система управления охраной труда - комплект документов')
            ->withCipher('suot200.1')
            ->withPrice(new Amount(350.00, new Currency('RUB')))
            ->withFilename(new Filename('suot200.1.rar'))
            ->withSlug(new Slug('suot'))
            ->withUpdatedAt(new \DateTimeImmutable())
            ->build();

        $manager->persist($product);

        $manager->persist($anotherProduct);

        $manager->flush();
    }
}