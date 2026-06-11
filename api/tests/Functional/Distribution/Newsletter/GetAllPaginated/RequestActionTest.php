<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Newsletter\GetAllPaginated;

use App\Distribution\Entity\Newsletter\NewsletterRepository;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/distributions/newsletters', [
            'page' => 1,
            'perPage' => 20,
        ]));

        $this->assertSame(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'newsletters' => [
                [
                    'id' => RequestFixture::NEWSLETTER_ID,
                    'subject' => 'Обновления сайта',
                    'templateId' => RequestFixture::TEMPLATE_ID,
                    'createdAt' => (new \DateTimeImmutable())->format('Y-m-d'),
                    'status' => 'created'
                ]
            ],
            'total' => 1,
            'currentPage' => 1,
            'perPage' => 20,
            'totalPages' => 1,
        ], $data);
    }
}