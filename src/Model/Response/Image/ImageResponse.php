<?php

declare(strict_types=1);

namespace App\Model\Response\Image;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ImageResponse', required: ['url', 'original_filename', 'mime_type', 'file_size'])]
final readonly class ImageResponse
{
    public function __construct(
        #[OA\Property(example: 'https://pub-xxxx.r2.dev/images/550e8400-e29b-41d4-a716-446655440000.png')]
        public string $url,
        #[OA\Property(example: 'screenshot.png')]
        public string $original_filename,
        #[OA\Property(example: 'image/png')]
        public string $mime_type,
        #[OA\Property(example: 204800, description: 'File size in bytes')]
        public int $file_size,
    ) {
    }
}
