<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\Newsletter;

use App\Distribution\Entity\Newsletter\Newsletter;
use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterStatus;
use App\Distribution\Entity\Newsletter\Status;
use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Test\Entity\NewsletterBuilder;
use PHPUnit\Framework\TestCase;

final class NewsletterTest extends TestCase
{
    public function testCreate(): void
    {
        $project = new Project(
            ProjectId::generate(),
            'Блог охраны труда'
        );

        $newsletter = new Newsletter(
            $id = NewsletterId::generate(),
            $subject = 'Обновления на сайте',
            $templateId = 'd255c7a2-64e7-4cb0-b419-69a2340e61b5',
            $status = Status::created(),
            $project->getId(),
            new \DateTimeImmutable(),
        );
        self::assertEquals($id, $newsletter->getId());
        self::assertEquals($subject, $newsletter->getSubject());
        self::assertEquals($templateId, $newsletter->getTemplateId());
        self::assertEquals($status, $newsletter->getStatus());
    }

    public function testLaunch(): void
    {
        $project = new Project(
            ProjectId::generate(),
            'Блог охраны труда'
        );

        $newsletter = new Newsletter(
            NewsletterId::generate(),
            'Обновления на сайте',
            'd255c7a2-64e7-4cb0-b419-69a2340e61b5',
            Status::created(),
            $project->getId(),
            new \DateTimeImmutable(),
        );
        $newsletter->launch();

        self::assertEquals('processed', $newsletter->getStatus()->getValue());
    }
    public function testLaunchAlready(): void
    {
        $project = new Project(
            ProjectId::generate(),
            'Блог охраны труда'
        );
        $newsletter = (new NewsletterBuilder())->withProjectId($project->getId())->build();

        $newsletter->launch();
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Newsletter is already processed.');
        $newsletter->launch();
    }

    public function testArchive(): void
    {
        $newsletter = (new NewsletterBuilder())->withProjectId(ProjectId::generate())->build();

        $newsletter->archive();
        self::assertEquals(NewsletterStatus::Archived->value, $newsletter->getStatus()->getValue());
    }

    public function testArchiveAlready(): void
    {
        $newsletter = (new NewsletterBuilder())->withProjectId(ProjectId::generate())->build();
        $newsletter->archive();
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Newsletter is already archived.');
        $newsletter->archive();
    }
}
