<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Project\GetAll;

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
    }
}