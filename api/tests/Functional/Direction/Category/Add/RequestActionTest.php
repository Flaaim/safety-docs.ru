<?php

namespace Test\Functional\Direction\Category\Add;

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
        $response = $this->app()->handle(self::json('POST', '/v1/directions/37e9c865-8401-4339-bb23-73a25b85e7b3/categories', [
            'title' => 'Обучение охраны труда - комплект документов',
            'description' => 'Собраны комплекты образцов документов по организации обучения по охране труда',
            'text' => 'Another simple text',
            'slug' => 'education',
        ]));

        self::assertEquals(201, $response->getStatusCode());
    }

    public function testAddExisting(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/directions/37e9c865-8401-4339-bb23-73a25b85e7b3/categories', [
            'title' => 'Обучение охраны труда - комплект документов',
            'description' => 'Собраны комплекты образцов документов по организации обучения по охране труда',
            'text' => 'Another simple text',
            'slug' => 'service',
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Category with slug service is exists.'
        ], $data);
    }
}