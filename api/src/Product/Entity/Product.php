<?php

namespace App\Product\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Column(type: 'json')]
    private array $images = [];
    #[ORM\Column(type: 'json', length: 255)]
    private array $formatDocuments = [];
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'product_id', unique: true)]
        private readonly ProductId $id,
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,
        #[ORM\Column(type: 'amount')]
        private Amount $amount,
        #[ORM\Column(type: 'file')]
        private File $file,
        #[ORM\Column(type: 'string', length: 25)]
        private string $cipher,
        #[ORM\Column(type: 'product_slug', length: 35)]
        private Slug $slug,
        #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
        private \DateTimeImmutable $updatedAt,
        #[ORM\Column(type: 'integer')]
        private int $totalDocuments,
        #[ORM\Column(type: 'json', length: 255)]
        /** @var array<FormatDocument> */
         array $formatDocuments
    ){
        Assert::greaterThanEq($this->totalDocuments, 0, 'Total documents cannot be negative.');
        $this->setFormatDocuments($formatDocuments);
    }
    public function getId(): ProductId
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getAmount(): Amount
    {
        return $this->amount;
    }
    public function getFile(): File
    {
        return $this->file;
    }
    public function getCipher(): string
    {
        return $this->cipher;
    }
    public function getSlug(): Slug
    {
        return $this->slug;
    }
    public function getTotalDocuments(): int
    {
        return $this->totalDocuments;
    }
    public function getFormatDocuments(): array
    {
        return array_map(
            static fn(string $format) => FormatDocument::from($format),
            $this->formatDocuments
        );
    }
    private function setFormatDocuments(array $formatDocuments): void
    {
        $this->formatDocuments = array_map(
            static fn(FormatDocument $format) => $format->value,
            $formatDocuments
        );
    }
    public function getImages(): array
    {
        return $this->images;
    }
    public function attachImage(string $imagePath): void
    {
        if (!in_array($imagePath, $this->images, true)) {
            $this->images[] = $imagePath;
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function removeImage(string $imagePath): void
    {
        $this->images = array_values(array_filter(
            $this->images,
            static fn(string $image) => $image !== $imagePath
        ));

        $this->updatedAt = new \DateTimeImmutable();
    }
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function update(
        string $name,
        string $cipher,
        Slug $slug,
        Amount $amount,
        File $file,
        int $totalDocuments,
        array $formatDocuments,
        \DateTimeImmutable $updatedAt = new \DateTimeImmutable(),
    ): void
    {
        $this->name = $name;
        $this->cipher = $cipher;
        $this->slug = $slug;
        $this->amount = $amount;
        $this->file = $file;
        $this->updatedAt = $updatedAt;
        $this->formatDocuments = $formatDocuments;
        $this->totalDocuments = $totalDocuments;
    }
}