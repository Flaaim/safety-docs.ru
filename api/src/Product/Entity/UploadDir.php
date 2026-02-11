<?php

namespace App\Product\Entity;

use App\Product\Service\ValidatePath;
use App\Shared\Domain\Service\Template\RootPath;

class UploadDir
{
    private ?string $targetPath = null;
    private ValidatePath $validatePath;
    private RootPath $rootPath;
    public function __construct(RootPath $uploadDir, ValidatePath $validatePath)
    {
        $this->validatePath = $validatePath;
        $this->rootPath = $uploadDir;
    }
    public function getValue(): ?string
    {
        return $this->targetPath;
    }
    public function setTargetPath(string $targetPath): void
    {
        $this->ensurePathValid($targetPath, $this->validatePath);
        $this->targetPath = $this->buildTargetPath($this->rootPath, $targetPath);
    }
    public function ensurePathValid(string $targetPath, ValidatePath $validatePath): void
    {
        if(!$validatePath->validate($targetPath)){
            throw new \DomainException(
                sprintf('Target path "%s" is not valid', $targetPath)
            );
        }
    }

    private function buildTargetPath(RootPath $uploadDir, string $targetPath): string
    {
        return rtrim($uploadDir->getValue(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . ltrim($targetPath, DIRECTORY_SEPARATOR);
    }

}