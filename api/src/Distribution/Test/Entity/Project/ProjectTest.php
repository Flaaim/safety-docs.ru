<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\Project;

use App\Distribution\Entity\Project\Contact;
use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    public function testCreate(): void
    {
        $project = new Project(
            $id = ProjectId::generate(),
            $name = 'one',
            $contacts = new ArrayCollection([new Contact('test@email.ru')])
        );

        self::assertEquals($id, $project->getId()->getValue());
        self::assertEquals($name , $project->getName());
        self::assertEquals($contacts->toArray() , $project->getContacts());
    }

    public function testHasContact(): void
    {
        $project = new Project(
            ProjectId::generate(),
            'one',
            new ArrayCollection([$existing = new Contact('existing@email.ru')])
        );

        self::assertTrue($project->hasContact($existing));
        self::assertFalse($project->hasContact(new Contact('new@email.ru')));
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
        $distribution = new Project(
            ProjectId::generate(),
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

    public function testUnsubscribeContact(): void
    {
        $existingContacts = [
            new Contact('one@mail.ru'),
            new Contact('two@mail.ru'),
        ];

        $distribution = new Project(
            ProjectId::generate(),
            'one',
            new ArrayCollection($existingContacts)
        );

        $distribution->unsubscribeContact(new Contact('one@mail.ru'));
        $contact = $distribution->getContacts()[0];

        /** @var Contact $contact */
        self::assertTrue($contact->isUnsubscribed());
    }
}