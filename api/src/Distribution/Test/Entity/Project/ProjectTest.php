<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\Project;

use App\Distribution\Entity\Project\Contact;
use App\Distribution\Entity\Project\DTO\ContactDTO;
use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use function DI\value;

final class ProjectTest extends TestCase
{
    public function testCreate(): void
    {
        $project = new Project(
            $id = ProjectId::generate(),
            $name = 'one'
        );

        self::assertEquals($id, $project->getId()->getValue());
        self::assertEquals($name, $project->getName());
        self::assertEmpty($project->getContacts());
    }

    public function testHasContact(): void
    {
        $project1 = new Project(ProjectId::generate(), 'one');
        $project2 = new Project(ProjectId::generate(), 'two');

        $project1->import([new ContactDTO('one@email.ru')]);
        self::assertFalse($project1->hasContact('new@email.ru', $project1->getId()));
        self::assertFalse($project2->hasContact('one@email.ru', $project2->getId()));
    }

    public function testImport(): void
    {
        $existingContacts = [
            new ContactDTO('one@mail.ru'),
            new ContactDTO('two@mail.ru'),
        ];

        $newContacts = [
            new ContactDTO('one@mail.ru'),
            new ContactDTO('three@mail.ru'),
            new ContactDTO('four@mail.ru'),
        ];
        $project = new Project(
            ProjectId::generate(),
            'one',
        );
        $project->import($existingContacts);
        $project->import($newContacts);

        self::assertCount(4, $project->getContacts());
    }
    public function testImportNotContactDTO(): void
    {
        $newContacts = [
            new \stdClass(),
        ];
        $project = new Project(
            ProjectId::generate(),
            'one',
        );

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Importing contacts must be an instance of ContactDTO');
        $project->import($newContacts);
    }
    public function testUnsubscribeContact(): void
    {
        $contacts = [
            new ContactDTO('one@mail.ru'),
            new ContactDTO('two@mail.ru'),
        ];

        $project = new Project(
            ProjectId::generate(),
            'one'
        );
        $project->import($contacts);

        $project->unsubscribeContact('one@mail.ru', $project->getId());
        $contact = $project->getContacts()[0];

        /** @var Contact $contact */
        self::assertTrue($contact->isUnsubscribed());
    }

    public function testGetSubscribedContacts(): void
    {
        $contacts = [
            new ContactDTO('one@mail.ru'),
            new ContactDTO('two@mail.ru'),
        ];
        $project = new Project(
            ProjectId::generate(),
            'one'
        );
        $project->import($contacts);
        $project->unsubscribeContact('two@mail.ru', $project->getId());
        $project->unsubscribeContact('one@mail.ru', $project->getId());

        $contact = $project->getSubscribedContacts();

        self::assertCount(0, $contact);

    }
}
