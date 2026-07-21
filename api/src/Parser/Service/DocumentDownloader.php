<?php

declare(strict_types=1);

namespace App\Parser\Service;

use App\Parser\Entity\DocumentAttachment;
use App\Shared\Domain\Service\File\DirectoryCreatorInterface;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class DocumentDownloader
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly ClientInterface $client,
        private readonly FileSystemPathInterface $fileSystemPath,
        private readonly DirectoryCreatorInterface $directoryCreator,
        private readonly LoggerInterface $logger
    ) {
    }

    public function download(string $relativePathDir, DocumentAttachment $documentAttachment, string $cookie): string
    {
        $cleanExtension = ltrim($documentAttachment->extension, '.');
        $filename = Uuid::uuid4()->toString() . '.' . $cleanExtension;

        $filePath = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $relativePathDir
            . DIRECTORY_SEPARATOR . $filename;

        $this->directoryCreator->createDirectory(dirname($filePath));
        try {
            $this->client->request('GET', 'https://1otruda.ru' . $documentAttachment->url, [
                'cookies' => false,
                'sink'    => $filePath,
                'timeout' => 30.0,
                'connect_timeout' => 5.0,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept'     => '*/*',
                    'Cookie'     => $cookie,
                ]
            ]);

            return $filename;
        } catch (\Throwable $throwable) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->logger->error('Не удалось скачать документ: ' . $throwable->getMessage());
            throw new \DomainException($throwable->getMessage());
        }
    }

    public function replace(string $relativePathDir, string $oldFilename, DocumentAttachment $documentAttachment, string $cookie): string
    {
        $oldFilePath = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $relativePathDir
            . DIRECTORY_SEPARATOR . $oldFilename;

        if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }

        return $this->download($relativePathDir, $documentAttachment, $cookie);
    }
}
