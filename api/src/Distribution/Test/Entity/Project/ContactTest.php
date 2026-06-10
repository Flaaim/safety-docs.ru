<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\Project;

use App\Distribution\Entity\Project\Contact;
use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;


final class ContactTest extends TestCase
{
    private Project $project;
    public function setUp(): void
    {
        $this->project = new Project(
          ProjectId::generate(),
          'one',
        );
    }
    public function testCreate(): void
    {
        $contact = new Contact(
            Uuid::uuid4()->toString(),
            $email = 'email@test.ru',
            $this->project
        );
        self::assertEquals($email, $contact->getEmail());
        self::assertFalse($contact->isUnsubscribed());
    }
    public function testInvalid(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new Contact(Uuid::uuid4()->toString(), 'invalid', $this->project);
    }
    public function testCase(): void
    {
        $email = mb_strtoupper('email@test.ru');
        $contact = new Contact(Uuid::uuid4()->toString(), $email, $this->project);
        self::assertEquals(mb_strtolower($email), $contact->getEmail());
    }

    public function testEmpty(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new Contact(Uuid::uuid4()->toString(), '', $this->project);
    }
    public function testEquals(): void
    {
        $contact1 = new Contact(Uuid::uuid4()->toString(), $email1 = 'contact1@mail.com', $this->project);
        $contact2 = new Contact(Uuid::uuid4()->toString(), $email2 = 'contact2@mail.com', $this->project);

        self::assertTrue($contact1->isEquals($email1));
        self::assertFalse($contact1->isEquals($email2));
    }

    public function testUnsubscribed(): void
    {
        $contact = new Contact(Uuid::uuid4()->toString(), 'email@test.ru', $this->project);
        $contact->unsubscribe();
        self::assertTrue($contact->isUnsubscribed());
    }
}