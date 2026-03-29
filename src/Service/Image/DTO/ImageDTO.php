<?php

declare(strict_types=1);

namespace App\Service\Image\DTO;

final readonly class ImageDTO
{
    public function __construct(
        public string $url,
        public string $originalFilename,
        public string $mimeType,
        public int $fileSize,
    ) {
    }
}
