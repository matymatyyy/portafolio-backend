<?php

declare(strict_types=1);

namespace App\Converter\User;

use App\Domain\User\User;
use App\Service\User\DTO\UserDTO;

final class UserDomainConverter
{
    public function toDTO(User $user): UserDTO
    {
        return new UserDTO(
            id: $user->id()
                ->value(),
            name: $user->name(),
            email: $user->email()
                ->value(),
            createdAt: $user->createdAt(),
            updatedAt: $user->updatedAt(),
        );
    }
}
