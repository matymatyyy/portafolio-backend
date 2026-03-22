<?php

declare(strict_types=1);

namespace App\Converter\User;

use App\Model\Request\User\UpdateUserRequest;
use App\Service\User\DTO\UpdateUserDTO;

final class UpdateUserRequestConverter
{
    public function fromRequest(string $id, UpdateUserRequest $request): UpdateUserDTO
    {
        return new UpdateUserDTO(
            id: $id,
            name: $request->name,
            email: $request->email,
            plainPassword: $request->password,
        );
    }
}
