<?php

declare(strict_types=1);

namespace App\Distribution\Service;

use App\Distribution\Entity\Distribution\Contact;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use League\Csv\Reader;


final class ContactFileImporter implements ContactFileImporterInterface
{
    public function __construct(
        private readonly FileSystemPathInterface $fileSystemPath
    ) {}

    public function import(string $path): array
    {
        $reader = Reader::from($this->fileSystemPath->getValue(). DIRECTORY_SEPARATOR . $path);
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();
        $result = [];
        foreach ($records as $record) {
            if(isset($record['email'])){
                $result[] = new Contact($record['email']);
            }
        }
        return $result;
    }
}