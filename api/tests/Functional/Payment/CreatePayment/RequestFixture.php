<?php

namespace Test\Functional\Payment\CreatePayment;

use App\Template\Entity\Document\DocumentId;
use App\Template\Test\Builder\DocumentBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{
    public const DOCUMENT_ID = '94ea4981-150e-4123-a3d6-ec004559fb02';
    public function load(ObjectManager $manager): void
    {
        $document = (new DocumentBuilder())
            ->withId(new DocumentId(self::DOCUMENT_ID))
            ->build();

        $manager->persist($document);

        $manager->flush();
    }
}