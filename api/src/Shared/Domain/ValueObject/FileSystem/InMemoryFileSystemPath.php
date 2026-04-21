<?php

namespace App\Shared\Domain\ValueObject\FileSystem;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class InMemoryFileSystemPath implements FileSystemPathInterface
{
    private string $value;
    private vfsStreamDirectory $root;
    private bool $isVfs;
    private function __construct(bool $useVfs = true)
    {
        $this->isVfs = $useVfs;

        if($useVfs) {
            $this->root = vfsStream::setup('storage');
            $this->value = vfsStream::url('storage');
        }else {
            $this->value = sys_get_temp_dir() . '/phpunit_real_storage';
            if (!is_dir($this->value)) {
                mkdir($this->value, 0777, true);
            }
        }
    }
    public static function create(): self
    {
        return new self();
    }
    public static function createReal(): self
    {
        return new self(false);
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