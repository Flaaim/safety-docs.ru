<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\RemoveContactsFile;

use App\Distribution\Entity\File\FileId;
use App\Distribution\Entity\File\FileRepository;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    private readonly InMemoryFileSystemPath $fileSystem;

    private readonly FileRepository $files;
    public function setUp(): void
    {
        $this->fileSystem = InMemoryFileSystemPath::createReal();
        $container = $this->app()->getContainer();
        $this->files = $container->get(FileRepository::class);
        $this->createUploadFile();
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/distributions/contact-files/2037b554-6c53-4e2a-aed1-919b9fe48cef'));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'File is not found.'], $data);
    }
    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('DELETE', '/v1/distributions/contact-files/'. RequestFixture::FILE_ID));

        self::assertEquals(204, $response->getStatusCode());

        $file = $this->files->findById(new FileId(RequestFixture::FILE_ID));

        self::assertNull($file);
        self::assertFileDoesNotExist(
            $this->fileSystem->getValue(). DIRECTORY_SEPARATOR. RequestFixture::FILE_ID .
            DIRECTORY_SEPARATOR . RequestFixture::FILENAME,
        );
    }
    private function createUploadFile(): void
    {
        $file = $this->fileSystem->getValue() . DIRECTORY_SEPARATOR . RequestFixture::FILENAME;
        $result = file_put_contents($file, 'some_content');
        if(!$result){
            throw new \RuntimeException('Unable to write file');
        }
    }
    public function tearDown(): void
    {
        $this->fileSystem->clear();
    }
}