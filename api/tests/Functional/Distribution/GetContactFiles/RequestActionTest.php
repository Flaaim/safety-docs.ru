<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\GetContactFiles;

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
        $response = $this->app()->handle(self::json('GET', '/v1/distributions/contact-files?page=1&perPage=10'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'files' => [
                [
                    'id' => RequestFixture::FILE_2['id'],
                    'name' => RequestFixture::FILE_2['name'],
                    'date' => (new \DateTimeImmutable())->format('Y-m-d'),
                    'complete' => false,
                ],
                [
                    'id' => RequestFixture::FILE_1['id'],
                    'name' => RequestFixture::FILE_1['name'],
                    'date' => (new \DateTimeImmutable())->format('Y-m-d'),
                    'complete' => false,
                ]
            ],
            'total' => 2,
            'currentPage' => 1,
            'perPage' => 10,
            'totalPages' => 1,
        ], $data);
    }
}