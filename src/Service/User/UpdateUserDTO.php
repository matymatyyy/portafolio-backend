<?php

declare(strict_types=1);

namespace App\Service\User;

final readonly class UpdateUserDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $plainPassword = null,
    ) {
    }
}
