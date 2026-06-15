<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Newsletter\Archive;

use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterRepository;
use App\Distribution\Entity\Newsletter\NewsletterStatus;
use App\Distribution\Entity\Newsletter\StatusType;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

final class RequestAction extends WebTestCase
{
    private NewsletterRepository $newsletters;
    public function setUp(): void
    {
        $this->loadFixtures([RequestFixture::class]);
        $container = $this->app()->getContainer();

        $this->newsletters = $container->get(NewsletterRepository::class);
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/distributions/newsletters/'.RequestFixture::PROJECT_ID));

        self::assertEquals(204, $response->getStatusCode());


        $newsletter = $this->newsletters->findById(new NewsletterId(RequestFixture::PROJECT_ID));

        self::assertEquals(NewsletterStatus::Archived->value, $newsletter->getStatus()->getValue());

    }

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/distributions/newsletters/8cc625c6-b067-4b08-962d-b087e40d1f05'));

        self::assertEquals(400, $response->getStatusCode());

        self:self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Newsletter not found'], $data);
    }
}