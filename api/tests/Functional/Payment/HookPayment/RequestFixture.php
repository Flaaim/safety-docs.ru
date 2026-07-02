<?php

namespace Test\Functional\Payment\HookPayment;

use App\Template\Entity\Document\DocumentId;
use App\Template\Entity\Document\Filename;
use App\Template\Test\Builder\DocumentBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Test\Functional\Payment\PaymentBuilder;


class RequestFixture extends AbstractFixture
{
    public const DOCUMENT_ID = '765a3b6f-a938-4314-b248-9df1aaef8fce';
    public const FILENAME = 'b1c45173-d172-46d7-b1b7-ef015c1e9a48.docx';
    public function load(ObjectManager $manager): void
    {
        $document = (new DocumentBuilder())
            ->withId(new DocumentId(self::DOCUMENT_ID))
            ->withFilename(new Filename(self::FILENAME))
            ->build();

        $manager->persist($document);

        $payment = (new PaymentBuilder())
            ->withProductId(self::DOCUMENT_ID)
            ->withExternalId('hook_test_payment_id')
            ->build();

        $manager->persist($payment);

        $manager->flush();
    }
}