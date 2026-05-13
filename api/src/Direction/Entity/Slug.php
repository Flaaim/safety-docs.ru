<?php

namespace App\Direction\Entity;

use Collator;
use Transliterator;
use Webmozart\Assert\Assert;

class Slug
{
    private string $value;
    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'Slug cannot be empty.');
        Assert::regex($value, '/^[a-z0-9]+(?:-[a-z0-9]+)*$/');
        $this->value = $value;
    }
    public function getValue(): string
    {
        return $this->value;
    }
    public static function generate(string $title): self
    {
        $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');

        if ($transliterator === null) {
            throw new \RuntimeException('Transliterator extension is not properly configured.');
        }

        $transliterated = $transliterator->transliterate($title);
        if ($transliterated === false) {
            throw new \DomainException('Transliteration failed.');
        }
        $value = mb_strtolower($transliterated);
        $value = preg_replace('/[^a-zA-Z0-9]+/', '-', $value);
        if($value === null){
            throw new \RuntimeException('Transliteration value is null.');
        }
        $value = trim($value, '-');
        if ($value === '') {
            throw new \DomainException('Cannot generate slug from the given title.');
        }

        return new self($value);
    }
    public function equals(Slug $slug): bool
    {
        return $this->value === $slug->value;
    }
}
