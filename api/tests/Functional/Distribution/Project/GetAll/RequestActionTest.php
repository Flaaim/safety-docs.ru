<?php

declare(strict_types=1);

namespace Distribution\Project\GetAll;

use Test\Functional\Distribution\Project\GetAll\RequestFixture;
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
        $response = $this->app()->handle(self::json('GET', '/v1/distributions/projects'));

        $this->assertSame(200, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'projects' => [
                [
                    'id' => RequestFixture::PROJECT_ID,
                    'name' => RequestFixture::PROJECT_NAME,
                    'contacts' => []
                ]
            ]
        ], $data);
    }
}