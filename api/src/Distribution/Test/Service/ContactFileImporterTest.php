<?php

declare(strict_types=1);

namespace App\Distribution\Test\Service;

use App\Distribution\Service\ContactFileImporter;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use League\Csv\Writer;
use PHPUnit\Framework\TestCase;


final class ContactFileImporterTest extends TestCase
{
    private readonly FileSystemPathInterface $fileSystemPath;
    public function setUp(): void
    {
        $this->fileSystemPath = InMemoryFileSystemPath::create();
    }
    public function testImport(): void
    {
        $file = 'contacts.csv';
        $path = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $file;
        $this->createCsvFile($path);
        $importer = new ContactFileImporter($this->fileSystemPath);

        $result = $importer->import($file);

        self::assertCount(1, $result);
        self::assertEquals('test@email.ru', $result[0]->getEmail());
    }
    private function createCsvFile(string $path): void
    {
        $csvContent = "email\ntest@email.ru";
        file_put_contents($path, $csvContent);
    }

    public function tearDown(): void
    {
        $this->fileSystemPath->clear();
    }
}