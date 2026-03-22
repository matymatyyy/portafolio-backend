<?php

declare(strict_types=1);

namespace App\Converter\User;

use App\Model\Request\User\CreateUserRequest;
use App\Service\User\DTO\CreateUserDTO;

final class CreateUserRequestConverter
{
    public function fromRequest(CreateUserRequest $request): CreateUserDTO
    {
        return new CreateUserDTO(name: $request->name, email: $request->email, plainPassword: $request->password);
    }
}
