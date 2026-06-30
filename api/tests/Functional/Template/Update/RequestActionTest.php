<?php

namespace Test\Functional\Template\Update;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }
    public function testSuccessWithSameTitle(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0948', [
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
        ]));

        self::assertEquals(204, $response->getStatusCode());
    }
    public function testSuccessWithTitle(): void{
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0948', [
            'title' => 'Экология',
            'description' => 'Описание Экология',
            'text' => 'Текст Экология',
        ]));

        self::assertEquals(204, $response->getStatusCode());
    }
    public function testDirectionNotFound(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0950', [
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
        ]));

        self::assertEquals(400, $response->getStatusCode());
    }

    public function testSlugAlreadyTakenAnotherDirection(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0948', [
            'title' => 'Промышленная безопасность',
            'description' => 'Описание промышленная безопасность',
            'text' => 'Текст промышленная безопасность',
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Direction with this slug already exists.'
        ], $data);
    }
}

