<?php

namespace Test\Functional\Product\Images\Add;

use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;
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
        $image1 = $this->createUploadFile('image1.jpg', 'content', 'image/jpeg', UPLOAD_ERR_OK);
        $image2 = $this->createUploadFile('image2.jpg', 'content', 'image/jpeg', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/products/b38e76c0-ac23-4c48-85fd-975f32c8801f/images',
            [],
            ['images' => [$image1, $image2]]
        ));

        self::assertEquals(201, $response->getStatusCode());

        self::assertDirectoryExists('/tmp/phpunit_test_/b38e76c0-ac23-4c48-85fd-975f32c8801f');
    }
    public function testInvalid(): void
    {
        $image1 = $this->createUploadFile('image1.jpg', 'content', 'application/vnd.rar', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/products/b38e76c0-ac23-4c48-85fd-975f32c8801f/images',
            [],
            ['images' => [$image1]]
        ));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'uploadedImages[0]' => 'The mime type of the file is invalid (application/vnd.rar). Allowed mime types are image/jpeg, image/png.'
        ]], $data);
    }

    public function testEmpty(): void
    {
        $image1 = $this->createUploadFile('image1.jpg', 'content', 'image/jpeg', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/products/b38e76c0-ac23-4c48-85fd-975f32c8801f/images',
            [],
            ['images' => []]
        ));

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);
        self::assertEquals(['errors' => [
            'uploadedImages' => 'This value should not be blank.',
        ]], $data);
    }
    private function createUploadFile(string $name, string $content, string $type, int $error): UploadedFileInterface
    {
        $file1 = tempnam($this->fileSystem->getValue(), 'file1');
        $result = file_put_contents($file1, $content);
        if(!$result){
            throw new \RuntimeException('Unable to write file');
        }
        return new UploadedFile(
            $file1,
            $name,
            $type,
            filesize($file1),
            $error,
        );
    }
    public function tearDown(): void
    {
        $this->fileSystem->clear();

    }
}