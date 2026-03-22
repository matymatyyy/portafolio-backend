<?php

declare(strict_types=1);

namespace App\Service\User;

use DateTimeImmutable;

final readonly class UserDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
