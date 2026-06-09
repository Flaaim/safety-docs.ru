<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\Distribution;

use App\Distribution\Entity\Distribution\Contact;
use App\Distribution\Entity\Distribution\Distribution;
use App\Distribution\Entity\Distribution\DistributionId;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class DistributionTest extends TestCase
{
    public function testCreate(): void
    {
        $distribution = new Distribution(
            $id = DistributionId::generate(),
            $name = 'one',
            $contacts = new ArrayCollection([new Contact('test@email.ru')])
        );

        self::assertEquals($id, $distribution->getId()->getValue());
        self::assertEquals($name , $distribution->getName());
        self::assertEquals($contacts->toArray() , $distribution->getContacts());
    }

    public function testHasContact(): void
    {
        $distribution = new Distribution(
            DistributionId::generate(),
            'one',
            new ArrayCollection([$existing = new Contact('existing@email.ru')])
        );

        self::assertTrue($distribution->hasContact($existing));
        self::assertFalse($distribution->hasContact(new Contact('new@email.ru')));
    }

    public function testImport(): void
    {
        $existingContacts = [
            new Contact('one@mail.ru'),
            new Contact('two@mail.ru'),
        ];

        $newContacts = [
            new Contact('one@mail.ru'),
            new Contact('three@mail.ru'),
            new Contact('four@mail.ru'),
        ];
        $distribution = new Distribution(
            DistributionId::generate(),
            'one',
            new ArrayCollection($existingContacts)
        );

        $distribution->import($newContacts);

        self::assertEquals([
            new Contact('one@mail.ru'),
            new Contact('two@mail.ru'),
            new Contact('three@mail.ru'),
            new Contact('four@mail.ru'),
        ], $distribution->getContacts());
    }
}