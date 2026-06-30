<?php

declare(strict_types=1);

namespace Test\Functional\Document\MultipleUpload;

use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = InMemoryFileSystemPath::createReal();
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testUploadInvalidMimeFile(): void
    {

    }
    public function testParentCategoryNotAllowed(): void
    {
        $file = $this->createUploadFile('инструкция.docx', 'content', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/v1/documents/bulk', [
            'name' => 'Инструкция',
            'categoryId' => RequestFixture::CHILD_CATEGORY_ID,
            'amount' => 150.00,
            'files' => [$file]
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Uploading to children category is prohibited.'
        ], $data);
    }

    public function testCategoryNotFound(): void
    {
        $file = $this->createUploadFile('инструкция.docx', 'content', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/v1/documents/bulk', [
            'name' => 'Инструкция',
            'categoryId' => 'a6d6e8ee-81c9-488b-b562-2eea9ec26c00',
            'amount' => 150.00,
            'files' => [$file]
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Category not found.'
        ], $data);
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::formData('POST', '/v1/documents/bulk'));
        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = $response->getBody()->getContents());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'name' => 'This value should not be blank.',
            'categoryId' => 'This value should not be blank.',
            'amount' => 'This value should be greater than 0.',
            'files[0]' => 'This value should be of type Psr\Http\Message\UploadedFileInterface.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::formData('POST', '/v1/documents/bulk', [
            'name' => 'something',
            'categoryId' => 'something',
            'amount' => -1,
            'files' => []
        ]));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = $response->getBody()->getContents());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'categoryId' => 'This is not a valid UUID.',
            'amount' => 'This value should be greater than 0.',
            'files' => 'This value should not be blank.'
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
}