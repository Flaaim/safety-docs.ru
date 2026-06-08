<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\UploadContactsFile;

use App\Distribution\Entity\File\FileRepository;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use Doctrine\ORM\EntityManagerInterface;
use org\bovigo\vfs\vfsStream;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;
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
        $em = $container->get(EntityManagerInterface::class);
        $this->files = new FileRepository($em);
    }

    public function testNoFile(): void
    {
        $file = $this->createUploadFile('contacts.csv', 'content', 'text/csv', UPLOAD_ERR_NO_FILE);

        $response = $this->app()->handle(self::formData('POST', '/v1/distributions/contact-files', [],
            ['file' => $file]
        ));

        $this->assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'File is required.'], $data);

    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::formData('POST', '/v1/distributions/contact-files'));

        $this->assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'File is required.'], $data);
    }

    public function testInvalidMimeTypeFile(): void
    {
        $file = $this->createUploadFile('contacts.csv', 'content', 'text/html', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/v1/distributions/contact-files', [], ['file' => $file]));
        $this->assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());
        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'file' => 'The mime type of the file is invalid (text/html). Allowed mime types are text/csv.'
        ]], $data);
    }
    public function testInvalidExtensionFile(): void
    {
        $file = $this->createUploadFile('contacts.txt', 'content', 'text/csv', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/v1/distributions/contact-files', [], ['file' => $file]));
        $this->assertEquals(422, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());
        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'file' => 'The extension of the file is invalid (txt). Allowed extensions are csv.'
        ]], $data);
    }

    public function testSuccess(): void
    {
        $file = $this->createUploadFile('contacts.csv', 'content', 'text/csv', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/v1/distributions/contact-files', [], ['file' => $file]));

        $this->assertEquals(204, $response->getStatusCode());

        $file = $this->files->findByName('contacts.csv');
        self::assertNotNull($file);
        self::assertEquals('contacts.csv', $file->getName());
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