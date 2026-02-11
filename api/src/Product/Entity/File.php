<?php

namespace App\Product\Entity;

use App\Shared\Domain\Service\Template\RootPath;
use Webmozart\Assert\Assert;

class File
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
        $this->fullPath = DIRECTORY_SEPARATOR .
            trim($value->getValue(), '/') . DIRECTORY_SEPARATOR . $this->value;
    }
    public function getFile(): string
    {
        if($this->fullPath === null) {
            throw new \DomainException('File path not merge with root path.');
        }
        if(!$this->exists())
        {
            throw new \DomainException('File not exists.');
        }
        return $this->fullPath;
    }
    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function getValue(): string
    {
        return $this->value;
    }
    private function exists(): bool
    {
        return file_exists($this->getFullPath());
    }
}