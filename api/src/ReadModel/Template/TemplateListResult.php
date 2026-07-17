<?php

declare(strict_types=1);

namespace App\ReadModel\Template;

/**
 * Paginated result of Template rows for admin UI.
 *
 * @psalm-immutable
 */
final class TemplateListResult implements \JsonSerializable
{
    /**
     * @param list<TemplateRow> $templates
     */
    public function __construct(
        public readonly array $templates,
        public readonly int $total,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly int $totalPages,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'templates' => $this->templates,
            'total' => $this->total,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
            'totalPages' => $this->totalPages,
        ];
    }
}
