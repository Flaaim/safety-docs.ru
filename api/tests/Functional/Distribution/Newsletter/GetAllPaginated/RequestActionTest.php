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
        $response = $this->app()->handle(self::json('POST', '/v1/distributions/newsletters'));

        $this->assertSame(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            [
                'newsletters' => [
                    [
                        'subject' => 'Обновления сайта',
                        'templateId' => 'd4d10922-471d-482a-873e-86f0d9d3d144'
                    ]
                ],
                'total' => 1,
                'currentPage' => 1,
                'perPage' => 20,
                'totalPages' => 1,
            ]
        ], $data);
    }
}