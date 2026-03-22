<?php

declare(strict_types=1);

namespace App\Converter\User;

use App\Model\Request\User\UserRequest;
use App\Service\User\CreateUserDTO;
use App\Service\User\UpdateUserDTO;

final class UserRequestToUserConverter
{
    public function fromCreateRequest(UserRequest $request): CreateUserDTO
    {
        return new CreateUserDTO(
            name: $request->name,
            email: $request->email,
            plainPassword: (string) $request->password,
        );
    }

    public function fromUpdateRequest(string $id, UserRequest $request): UpdateUserDTO
    {
        return new UpdateUserDTO(
            id: $id,
            name: $request->name,
            email: $request->email,
            plainPassword: $request->password,
        );
    }
}
