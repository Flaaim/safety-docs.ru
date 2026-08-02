<?php

declare(strict_types=1);

namespace App\Template\Entity\Document;

use App\Template\Entity\Category\Category;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'documents')]
class Document
{
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'document_id', unique: true)]
        private DocumentId $id,
        #[ORM\Column(type: 'string', length: 512)]
        private string $name,
        #[ORM\Column(type: 'document_amount')]
        private Amount $amount,
        #[ORM\Column(type: 'filename')]
        private Filename $filename,
        #[ORM\Column(type: 'string', length: 512)]
        private string $slug,
        #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'documents')]
        #[ORM\JoinColumn(
            name: 'category_id',
            referencedColumnName: 'category_id',
            nullable: false,
            onDelete: 'RESTRICT'
        )
        ]
        private Category $category
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $category->addDocument($this);
    }

    public function getId(): DocumentId
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
    public function getFilename(): Filename
    {
        return $this->filename;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function getSlug(): string
    {
        return $this->slug;
    }
    public function getCategory(): Category
    {
        return $this->category;
    }

    public function refreshUploadedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    public function updateFilename(Filename $filename): void
    {
        $this->filename = $filename;
    }

    public function updateSlug(string $slug): void
    {
        $this->slug = $slug;
    }
}
