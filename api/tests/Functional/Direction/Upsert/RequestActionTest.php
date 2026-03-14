<?php

namespace Test\Functional\Direction\Upsert;

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
        $response = $this->app()->handle(self::json('POST', '/v1/directions/upsert', [
            'direction_id' => '4428cf8d-7d57-4ded-ba6a-274e344dee02',
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'firesafety'
        ]));

        self::assertEquals(201, $response->getStatusCode());

    }

    public function testAddFailExisting(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/directions/upsert', [
            'direction_id' => '4428cf8d-7d57-4ded-ba6a-274e344dee02',
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'safety'
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertEquals(['message' => 'Direction slug already exists.'], $data);
    }

    public function testUpdateExistingWithDifferentSlug(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/directions/upsert', [
            'direction_id' => '9dc41818-1c99-4b3c-b1bc-7c64ee7a0948',
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'firesafety'
        ]));

        self::assertEquals(201, $response->getStatusCode());
    }

    public function testUpdateExistingSuccessSameSlug(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/directions/upsert', [
            'direction_id' => '9dc41818-1c99-4b3c-b1bc-7c64ee7a0948',
            'title' => 'Пожарная безопасность',
            'description' => 'Описание пожарная безопасность',
            'text' => 'Текст пожарная безопасность',
            'slug' => 'safety'
        ]));

        self::assertEquals(201, $response->getStatusCode());

    }

    public function testUpdateConflictWithOtherEntitySlug(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/directions/upsert', [
            'direction_id' => '9582c2ff-e788-46f6-94f9-6b7d73b309bd',
            'title' => 'Охрана труда',
            'description' => 'Описание охрана труда',
            'text' => 'Текст охрана труда',
            'slug' => 'safety'
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());
        $data = Json::decode($body);

        self::assertEquals(['message' => 'New slug is already taken by another direction.'], $data);
    }

}