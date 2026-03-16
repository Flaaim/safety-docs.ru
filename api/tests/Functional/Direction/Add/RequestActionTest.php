<?php

namespace Test\Functional\Direction\Add;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testAddNew(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/directions/add', [
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'firesafety'
        ]));

        self::assertEquals(201, $response->getStatusCode());

    }

    public function testAddFailExisting(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/directions/add', [
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'safety'
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertEquals(['message' => "Direction with slug safety is exists"], $data);
    }

}