<?php

namespace App\Product\Test\Service;

use App\Product\Service\File\RandomFileNameGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class RandomFileNameGeneratorTest extends TestCase
{
    /** @psalm-var  UploadedFileInterface&MockObject  */
    private UploadedFileInterface $uploadedFile;
    public function setUp(): void
    {
        $this->uploadedFile = $this->createMock(UploadedFileInterface::class);
    }
    public function testNullClientFilename(): void
    {
        $generator = new RandomFileNameGenerator();
        $this->uploadedFile->expects($this->once())->method('getClientFilename')->willReturn(null);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Client file name cannot be null.');
        $generator->generate($this->uploadedFile);
    }
}