<?php

declare(strict_types=1);

namespace App\Model\Response\User;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'PaginatedResponse')]
final readonly class PaginatedResponse
{
    /**
     * @param UserResponse[] $data
     * @param array<string, int> $meta
     */
    public function __construct(
        #[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/UserResponse'))]
        public array $data,
        #[OA\Property(type: 'object', properties: [
            new OA\Property(property: 'total', type: 'integer', example: 100),
            new OA\Property(property: 'page', type: 'integer', example: 1),
            new OA\Property(property: 'limit', type: 'integer', example: 10),
            new OA\Property(property: 'total_pages', type: 'integer', example: 10),
        ])]
        public array $meta,
    ) {
    }
}
