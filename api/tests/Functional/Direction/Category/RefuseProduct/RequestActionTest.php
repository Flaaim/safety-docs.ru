<?php

namespace Test\Functional\Direction\Category\RefuseProduct;

use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testRefuseSuccess(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/categories/15823c37-3358-44be-96dc-363d56bde91c/product'));

        self::assertEquals(204, $response->getStatusCode());
    }

}