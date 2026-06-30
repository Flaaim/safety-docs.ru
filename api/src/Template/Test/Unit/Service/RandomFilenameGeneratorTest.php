<?php

declare(strict_types=1);

namespace App\Template\Test\Unit\Service;

use App\Template\Service\File\RandomFilenameGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

final class RandomFilenameGeneratorTest extends TestCase
{
    /** @psalm-var  UploadedFileInterface&MockObject  */
    private UploadedFileInterface $uploadedFile;
    public function setUp(): void
    {
        $this->uploadedFile = $this->createMock(UploadedFileInterface::class);
    }

    public function testSuccess(): void
    {
        $generator = new RandomFilenameGenerator();
        $this->uploadedFile->expects($this->once())->method('getClientFilename')->willReturn('filename.docx');

        $result = $generator->generate($this->uploadedFile);
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\.docx/';
        self::assertMatchesRegularExpression($pattern, $result);
    }

    public function testNullClientFilename(): void
    {
        $generator = new RandomFilenameGenerator();
        $this->uploadedFile->expects($this->once())->method('getClientFilename')->willReturn(null);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Client file name cannot be null.');
        $generator->generate($this->uploadedFile);
    }
}