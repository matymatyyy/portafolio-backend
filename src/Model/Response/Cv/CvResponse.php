<?php

declare(strict_types=1);

namespace App\Model\Response\Cv;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'CvResponse', required: [
    'id',
    'original_filename',
    'mime_type',
    'file_size',
    'is_active',
    'uploaded_at',
    'updated_at',
])]
final readonly class CvResponse
{
    public function __construct(
        #[OA\Property(example: '550e8400-e29b-41d4-a716-446655440000')]
        public string $id,
        #[OA\Property(example: 'matias_cv.pdf')]
        public string $original_filename,
        #[OA\Property(example: 'application/pdf')]
        public string $mime_type,
        #[OA\Property(example: 204800, description: 'File size in bytes')]
        public int $file_size,
        #[OA\Property(example: true)]
        public bool $is_active,
        #[OA\Property(example: '2024-01-01T00:00:00+00:00')]
        public string $uploaded_at,
        #[OA\Property(example: '2024-01-01T00:00:00+00:00')]
        public string $updated_at,
    ) {
    }
}
