<?php

namespace App\Product\Entity;

use App\Shared\Domain\ValueObject\UpdatedAt;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'product_id', unique: true)]
    private ProductId $id;
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;
    #[ORM\Column(type: 'string', length: 25)]
    private string $cipher;
    #[ORM\Column(type: 'product_slug')]
    private Slug $slug;
    #[ORM\Column(type: 'amount')]
    private Amount $amount;
    #[ORM\Column(type: 'file')]
    private File $file;
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $updatedAt;
    public function __construct(
        ProductId $id,
        string $name,
        Amount $amount,
        File $file,
        string $cipher,
        Slug $slug,
        \DateTimeImmutable $updatedAt
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->amount = $amount;
        $this->file = $file;
        $this->cipher = $cipher;
        $this->slug = $slug;
        $this->updatedAt = $updatedAt;
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
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function update(
        string $name,
        Amount $amount,
        File $file,
        string $cipher,
        \DateTimeImmutable $updatedAt = new \DateTimeImmutable()
    ): void
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->file = $file;
        $this->cipher = $cipher;
        $this->updatedAt = $updatedAt;
    }
}