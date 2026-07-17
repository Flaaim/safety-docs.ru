<?php

declare(strict_types=1);

namespace App\ReadModel\Template;

/**
 * Read-model row for admin Template (Document) list.
 * Does not map to the Document aggregate — denormalized for UI.
 */
final class TemplateRow implements \JsonSerializable
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $directionName,
        public readonly string $categoryName,
        public readonly string $createdAt,
        public readonly string $status,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromArray(array $row): self
    {
        $createdAt = $row['createdAt'] ?? $row['created_at'] ?? '';
        if ($createdAt instanceof \DateTimeInterface) {
            $createdAt = $createdAt->format(\DateTimeInterface::ATOM);
        } elseif (is_string($createdAt) && $createdAt !== '') {
            $createdAt = (new \DateTimeImmutable($createdAt))->format(\DateTimeInterface::ATOM);
        }

        return new self(
            id: (string) $row['id'],
            name: (string) $row['name'],
            directionName: (string) ($row['directionName'] ?? $row['direction_name'] ?? ''),
            categoryName: (string) ($row['categoryName'] ?? $row['category_name'] ?? ''),
            createdAt: (string) $createdAt,
            // Document aggregate has no status column yet — default for admin list.
            status: (string) ($row['status'] ?? 'active'),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'directionName' => $this->directionName,
            'categoryName' => $this->categoryName,
            'createdAt' => $this->createdAt,
            'status' => $this->status,
        ];
    }
}
