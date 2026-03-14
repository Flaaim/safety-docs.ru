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
                    'title' => 'Охрана труда',
                    'description' => 'Собраны комплекты документов',
                    'text' => 'some text',
                    'slug' => 'safety'
                ]
            ],
            'total' => 1
        ], $data);
    }
}