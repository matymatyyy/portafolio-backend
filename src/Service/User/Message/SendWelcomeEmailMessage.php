<?php

declare(strict_types=1);

namespace App\Service\User\Message;

final readonly class SendWelcomeEmailMessage
{
    public function __construct(
        public string $email,
        public string $name,
    ) {
    }
}
