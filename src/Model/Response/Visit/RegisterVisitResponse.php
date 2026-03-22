<?php

declare(strict_types=1);

namespace App\Model\Response\Visit;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'RegisterVisitResponse', required: ['id', 'visited_at'])]
final readonly class RegisterVisitResponse
{
    public function __construct(
        #[OA\Property(example: '550e8400-e29b-41d4-a716-446655440000')]
        public string $id,
        #[OA\Property(example: '2024-01-01T00:00:00+00:00')]
        public string $visited_at,
    ) {
    }
}
