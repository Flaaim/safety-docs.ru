<?php

namespace App\Product\Test;

class TempDir
{
    private string $value;
    private function __construct()
    {
        $this->value = sys_get_temp_dir(). DIRECTORY_SEPARATOR . 'phpunit_test_';
        if(!file_exists($this->value)){
            mkdir($this->value, 0777, true);
        }
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
        $this->removeDirectory($this->value);
    }
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if(is_dir($path)) {
                $this->removeDirectory($path);
                rmdir($path);
            }else{
                unlink($path);
            }
        }

        if($dir === $this->value) {
            rmdir($dir);
        }
    }
}