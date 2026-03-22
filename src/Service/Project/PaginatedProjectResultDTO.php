<?php

declare(strict_types=1);

namespace App\Service\Project;

final readonly class PaginatedProjectResultDTO
{
    /**
     * @param ProjectDTO[] $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $limit,
    ) {
    }

    public function totalPages(): int
    {
        return (int) ceil($this->total / $this->limit);
    }
}
