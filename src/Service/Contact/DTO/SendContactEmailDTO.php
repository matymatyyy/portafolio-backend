<?php

declare(strict_types=1);

namespace App\Service\Contact\DTO;

final readonly class SendContactEmailDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $subject,
        public string $message,
    ) {
    }
}
