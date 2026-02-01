<?php

namespace App\Payment\Fixture;

use App\Payment\Entity\Email;
use App\Payment\Entity\Payment;
use App\Payment\Entity\Token;
use App\Product\Entity\Currency;
use App\Product\Entity\Price;
use App\Shared\Domain\ValueObject\Id;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class PaymentFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $payment = new Payment(
            new Id(Uuid::uuid4()->toString()),
            new Email('test@app.ru'),
            'b38e76c0-ac23-4c48-85fd-975f32c8809f',
            new Price(450.00, new Currency('RUB')),
            new DateTimeImmutable('now'),
            new Token(Uuid::uuid4()->toString(), new DateTimeImmutable('+ 1 hours')),
        );
        $payment->setExternalId('30853db7-000f-5001-8000-13e37ec6e877');

        $manager->persist($payment);

        $manager->flush();
    }
}