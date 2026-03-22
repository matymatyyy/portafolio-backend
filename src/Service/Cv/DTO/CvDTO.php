<?php

declare(strict_types=1);

namespace App\Service\Cv\DTO;

use DateTimeImmutable;

final readonly class CvDTO
{
    public function __construct(
        public string $id,
        public string $originalFilename,
        public string $mimeType,
        public int $fileSize,
        public bool $isActive,
        public DateTimeImmutable $uploadedAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
