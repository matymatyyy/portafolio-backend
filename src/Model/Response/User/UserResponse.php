<?php

declare(strict_types=1);

namespace App\Model\Response\User;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'UserResponse', required: ['id', 'name', 'email', 'created_at', 'updated_at'],)]
final readonly class UserResponse
{
    public function __construct(
        #[OA\Property(example: '550e8400-e29b-41d4-a716-446655440000')]
        public string $id,
        #[OA\Property(example: 'John Doe')]
        public string $name,
        #[OA\Property(example: 'john@example.com')]
        public string $email,
        #[OA\Property(example: '2024-01-01T00:00:00+00:00')]
        public string $created_at,
        #[OA\Property(example: '2024-01-01T00:00:00+00:00')]
        public string $updated_at,
    ) {
    }
}
