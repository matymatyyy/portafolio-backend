<?php

declare(strict_types=1);

namespace App\Service\Cv\DTO;

final readonly class CvFileDTO
{
    public function __construct(
        public string $originalFilename,
        public string $mimeType,
        public int $fileSize,
        public string $fileContent,
    ) {
    }
}
