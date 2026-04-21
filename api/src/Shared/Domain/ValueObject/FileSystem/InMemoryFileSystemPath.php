<?php

namespace App\Shared\Domain\ValueObject\FileSystem;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class InMemoryFileSystemPath implements FileSystemPathInterface
{
    private string $value;
    private vfsStreamDirectory $root;
    private function __construct()
    {
        $this->root = vfsStream::setup('storage');
        $this->value = vfsStream::url('storage');
    }
    public static function create(): self
    {
        return new self();
    }
    public function getValue(): string
    {
        return $this->value;
    }
    public function clear(): void
    {
        vfsStream::setup('storage');
    }
}