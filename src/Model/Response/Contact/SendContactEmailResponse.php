<?php

declare(strict_types=1);

namespace App\Model\Response\Contact;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'SendContactEmailResponse', required: ['message'])]
final readonly class SendContactEmailResponse
{
    public function __construct(
        #[OA\Property(example: 'Contact email sent successfully.')]
        public string $message,
    ) {
    }
}
