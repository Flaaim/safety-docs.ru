<?php

namespace Functional\Direction\Category\Admin\GetAll;

use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/categories'));

        self::assertEquals(200, $response->getStatusCode());
    }
}