<?php

namespace App\Product\Entity;


use App\Shared\Domain\File\AttachableFileInterface;
use App\Shared\Domain\Service\Template\RootPath;
use Webmozart\Assert\Assert;

class File implements AttachableFileInterface
{
    private string $value;
    private ?string $fullPath = null;
    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = trim($value, '/');
    }
    public function mergeRoot(RootPath $value): void
    {
        if($this->fullPath !== null) {
            throw new \DomainException('Root path already merged.');
        }
        $this->fullPath = DIRECTORY_SEPARATOR .
            trim($value->getValue(), '/') . DIRECTORY_SEPARATOR . $this->value;
    }
    public function getFile(): string
    {
        if(!$this->exists()) {
            throw new \DomainException('File not exists.');
        }
        return $this->fullPath;
    }

    public function getValue(): string
    {
        return $this->value;
    }
    public function exists(): bool
    {
        if($this->fullPath === null) {
            return false;
        }
        return file_exists($this->fullPath);
    }
}