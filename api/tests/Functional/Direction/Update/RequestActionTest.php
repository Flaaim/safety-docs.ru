<?php

namespace Test\Functional\Direction\Update;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }
    public function testSuccessWithSameSlug(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0948', [
            'directionId' => '9dc41818-1c99-4b3c-b1bc-7c64ee7a0948',
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'safety'
        ]));

        self::assertEquals(204, $response->getStatusCode());
    }
    public function testSuccessWithNewSlug(): void{
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0948', [
            'directionId' => '9dc41818-1c99-4b3c-b1bc-7c64ee7a0948',
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'new-slug'
        ]));

        self::assertEquals(204, $response->getStatusCode());
    }
    public function testDirectionNotFound(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0950', [
            'directionId' => '9dc41818-1c99-4b3c-b1bc-7c64ee7a0950',
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'safety'
        ]));

        self::assertEquals(400, $response->getStatusCode());
    }

    public function testSlugAlreadyTakenAnotherDirection(): void
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/directions/9dc41818-1c99-4b3c-b1bc-7c64ee7a0948', [
            'directionId' => '9dc41818-1c99-4b3c-b1bc-7c64ee7a0948',
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'fire'
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Direction with this slug already exists.'
        ], $data);
    }
}

