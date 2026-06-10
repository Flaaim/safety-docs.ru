<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\Newsletter;

use App\Distribution\Entity\Newsletter\Newsletter;
use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\Status;
use PHPUnit\Framework\TestCase;

final class NewsletterTest extends TestCase
{
    public function testCreate(): void
    {
        $newsletter = new Newsletter(
            $id = NewsletterId::generate(),
            $subject = 'Обновления на сайте',
            $templateId = 'd255c7a2-64e7-4cb0-b419-69a2340e61b5',
            $status = Status::created()
        );

        self::assertEquals($id, $newsletter->getId());
        self::assertEquals($subject, $newsletter->getSubject());
        self::assertEquals($templateId, $newsletter->getTemplateId());
        self::assertEquals($status, $newsletter->getStatus());
    }
}
