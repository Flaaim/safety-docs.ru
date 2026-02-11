<?php

namespace App\Product\Test;

class TempDir
{
    private string $value;
    private function __construct()
    {
        $this->value = sys_get_temp_dir(). DIRECTORY_SEPARATOR . 'phpunit_test_';
        $result = mkdir($this->value, 0777, true);
        if(!$result) {
            throw new \DomainException('Could not create temp test directory');
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
}