<?php

declare(strict_types=1);

namespace App\Model\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ErrorResponse', required: ['error', 'message'],)]
final readonly class ErrorResponse
{
    /**
     * @param array<int, array<string, string>> $details
     */
    public function __construct(
        #[OA\Property(example: 'validation_error')]
        public string $error,
        #[OA\Property(example: 'Invalid request data.')]
        public string $message,
        #[OA\Property(type: 'array', items: new OA\Items(type: 'object'))]
        public array $details = [],
    ) {
    }
}
