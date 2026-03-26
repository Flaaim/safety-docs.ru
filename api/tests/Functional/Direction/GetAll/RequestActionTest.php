<?php

namespace Test\Functional\Direction\GetAll;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            RequestFixture::class
        ]);
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/directions'));

        $this->assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'directions' => [
                [
                    'id' => '37e9c865-8401-4339-bb23-73a25b85e7b3',
                    'title' => 'Охрана труда',
                    'description' => 'Собраны комплекты документов',
                    'text' => 'some text',
                    'slug' => 'safety',
                    'categories' => []
                ]
            ],
            'total' => 1
        ], $data);
    }
}