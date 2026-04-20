<?php

namespace Test\Functional\Product\Images\GetAll;

use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestActionTest extends WebTestCase
{
    private InMemoryFileSystemPath $fileSystem;
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([RequestFixture::class]);
        $this->fileSystem = InMemoryFileSystemPath::create();
    }
    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', 'v1/products/b38e76c0-ac23-4c48-85fd-975f32c8801f/images'));

        self::assertEquals(200, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals($data, [
            '/tmp/phpunit_test_/b38e76c0-ac23-4c48-85fd-975f32c8801f/image1.jpg',
            '/tmp/phpunit_test_/b38e76c0-ac23-4c48-85fd-975f32c8801f/image2.jpg',
            '/tmp/phpunit_test_/b38e76c0-ac23-4c48-85fd-975f32c8801f/image3.jpg'
        ]);
    }
    public function tearDown(): void
    {
        $this->fileSystem->clear();
    }
}