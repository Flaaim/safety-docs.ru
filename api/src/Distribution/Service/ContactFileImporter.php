<?php

declare(strict_types=1);

namespace App\Distribution\Service;

use App\Distribution\Entity\Project\DTO\ContactDTO;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use League\Csv\Reader;


final class ContactFileImporter implements ContactFileImporterInterface
{
    public function __construct(
        private readonly FileSystemPathInterface $fileSystemPath
    ) {}

    public function import(string $path): array
    {
        $fullPath = $this->fileSystemPath->getValue(). DIRECTORY_SEPARATOR . $path;
        try{
            if(!file_exists($fullPath)) {
                throw new \DomainException('File not found in filesystem.');
            }
            $reader = Reader::from($fullPath);
            $reader->setHeaderOffset(0);
            $records = $reader->getRecords();
            $result = [];
            foreach ($records as $record) {
                if(isset($record['email'])){
                    $result[] = new ContactDTO($record['email']);
                }
            }
            return $result;
        }catch (\Exception $exception){
            throw new \DomainException($exception->getMessage());
        }

    }
}