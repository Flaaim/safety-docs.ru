<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\UploadDir;
use App\Product\Service\ValidatePath;
use App\Shared\Domain\Service\Template\TemplatePath;
use PHPUnit\Framework\TestCase;

class UploadDirTest extends TestCase
{
    public function testSuccess(): void
    {
        $uploadDir = new UploadDir(
            $this->getTemplatePath(),
            $this->getValidatePath()
        );

        self::assertEquals(null, $uploadDir->getValue());
    }
    public function testInvalid(): void
    {
        $targetPath = '1974/1974.8.docx';
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage(sprintf('Target path "%s" is not valid', $targetPath));
        (new UploadDir(
            $this->getTemplatePath(),
            $this->getValidatePath()
        ))->setTargetPath($targetPath);

    }
    private function getTemplatePath(): TemplatePath
    {
        return new TemplatePath(sys_get_temp_dir());
    }
    private function getValidatePath(): ValidatePath
    {
        return new ValidatePath();
    }
}