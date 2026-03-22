<?php

declare(strict_types=1);

namespace App\Model\Request\User;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name is required.')]
        #[Assert\Length(min: 2, max: 255, minMessage: 'Name must be at least {{ limit }} characters.')]
        public readonly string $name = '',
        #[Assert\NotBlank(message: 'Email is required.')]
        #[Assert\Email(message: 'Please provide a valid email address.')]
        public readonly string $email = '',
        #[Assert\NotBlank(message: 'Password is required.')]
        #[Assert\Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters.')]
        public readonly string $password = '',
    ) {
    }
}
