<?php

declare(strict_types=1);

namespace Test\Functional\Template\Document\GetById;

use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testInvalid(): void
    {
        $request = $this->app()->handle(self::json(
            'GET',
            '/v1/directions/' . RequestFixture::DIRECTION_ID . '/categories/' . RequestFixture::CATEGORY_ID . '/documents/invalid',
        ));

        self::assertEquals(404, $request->getStatusCode());
    }
}
