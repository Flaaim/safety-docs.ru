<?php

declare(strict_types=1);

namespace Test\Functional\Template\Document\MultipleUpload;

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Document\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

final class RequestActionTest extends WebTestCase
{
    private InMemoryFileSystemPath $fileSystem;
    private DocumentRepository $documents;

    public function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = InMemoryFileSystemPath::createReal();
        $container = $this->app()->getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $this->documents = new DocumentRepository($em);
        $this->loadFixtures([RequestFixture::class]);
    }

    public function testParentCategoryNotAllowed(): void
    {
        $file = $this->createUploadFile('инструкция.docx', 'content', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/v1/directions/' . RequestFixture::DIRECTION_ID . '/categories/'. RequestFixture::PARENT_CATEGORY_ID . '/documents/bulk', [
            'name' => 'Инструкция',
            'amount' => 150.00,
            'files' => [$file]
        ]));

        self::assertEquals(400, $response->getStatusCode());

        self::assertJson($body = (string) $response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Cannot add a document, because the current category contains subcategories.'
        ], $data);
    }

    public function testCategoryNotFound(): void
    {
        $file = $this->createUploadFile('инструкция.docx', 'content', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', UPLOAD_ERR_OK);

        $response = $this->app()->handle(self::formData('POST', '/v1/directions/' . RequestFixture::DIRECTION_ID . '/categories/'. RequestFixture::CATEGORY_NOT_FOUND . '/documents/bulk', [
            'name' => 'Инструкция',
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
        $response = $this->app()->handle(self::formData('POST', '/v1/directions/' . RequestFixture::DIRECTION_ID . '/categories/'. RequestFixture::CHILD_CATEGORY_ID . '/documents/bulk'));
        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = $response->getBody()->getContents());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'amount' => 'This value should be greater than 0.',
            'files' => 'This value should not be blank.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $response = $this->app()->handle(self::formData('POST', '/v1/directions/' . RequestFixture::DIRECTION_ID . '/categories/'. RequestFixture::CHILD_CATEGORY_ID . '/documents/bulk', [
            'amount' => -1,
            'files' => []
        ]));

        self::assertEquals(422, $response->getStatusCode());

        self::assertJson($body = $response->getBody()->getContents());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'amount' => 'This value should be greater than 0.',
            'files' => 'This value should not be blank.'
        ]], $data);
    }

    public function testSuccessUpload(): void
    {
        $file = $this->createUploadFile(
            'инструкция.docx',
            'first-content',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            UPLOAD_ERR_OK
        );

        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/directions/' . RequestFixture::DIRECTION_ID . '/categories/' . RequestFixture::CHILD_CATEGORY_ID . '/documents/bulk',
            [
                'amount' => 150.00,
                'files' => [$file],
            ]
        ));

        self::assertEquals(201, $response->getStatusCode());

        $documents = $this->documents->findByCategoryIdAndName(
            new CategoryId(RequestFixture::CHILD_CATEGORY_ID),
            'инструкция.docx'
        );

        self::assertNotNull($documents);

        $templatePath = $this->app()->getContainer()->get(FileSystemPath::class)->getValue();
        $storedFile = $templatePath . DIRECTORY_SEPARATOR . $documents->getId()->getValue()
            . DIRECTORY_SEPARATOR . $documents->getFilename()->getValue();

        self::assertFileExists($storedFile);
        self::assertSame('first-content', file_get_contents($storedFile));
    }

    public function testOverwriteExistingDocumentByName(): void
    {
        $firstFile = $this->createUploadFile(
            'инструкция.docx',
            'first-content',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            UPLOAD_ERR_OK
        );

        $this->app()->handle(self::formData(
            'POST',
            '/v1/directions/' . RequestFixture::DIRECTION_ID . '/categories/' . RequestFixture::CHILD_CATEGORY_ID . '/documents/bulk',
            [
                'amount' => 150.00,
                'files' => [$firstFile],
            ]
        ));

        $existing = $this->documents->findByCategoryIdAndName(
            new CategoryId(RequestFixture::CHILD_CATEGORY_ID),
            'инструкция.docx'
        );

        self::assertNotNull($existing);
        $documentId = $existing->getId();
        $storedFilename = $existing->getFilename()->getValue();
        $createdAt = $existing->getCreatedAt();

        sleep(1);

        $secondFile = $this->createUploadFile(
            'инструкция.docx',
            'second-content',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            UPLOAD_ERR_OK
        );

        $response = $this->app()->handle(self::formData(
            'POST',
            '/v1/directions/' . RequestFixture::DIRECTION_ID . '/categories/' . RequestFixture::CHILD_CATEGORY_ID . '/documents/bulk',
            [
                'amount' => 200.00,
                'files' => [$secondFile],
            ]
        ));

        self::assertEquals(201, $response->getStatusCode());

        $this->app()->getContainer()->get(EntityManagerInterface::class)->clear();

        $updated = $this->documents->findByCategoryIdAndName(
            new CategoryId(RequestFixture::CHILD_CATEGORY_ID),
            'инструкция.docx'
        );

        self::assertNotNull($updated);
        self::assertTrue($documentId->equals($updated->getId()));
        self::assertSame($storedFilename, $updated->getFilename()->getValue());
        self::assertGreaterThan($createdAt->getTimestamp(), $updated->getCreatedAt()->getTimestamp());

        $templatePath = $this->app()->getContainer()->get(FileSystemPath::class)->getValue();
        $storedFile = $templatePath . DIRECTORY_SEPARATOR . $updated->getId()->getValue()
            . DIRECTORY_SEPARATOR . $updated->getFilename()->getValue();

        self::assertFileExists($storedFile);
        self::assertSame('second-content', file_get_contents($storedFile));
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