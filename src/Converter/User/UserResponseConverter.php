<?php

declare(strict_types=1);

namespace App\Converter\User;

use App\Model\Response\User\PaginatedResponse;
use App\Model\Response\User\UserResponse;
use App\Service\User\DTO\PaginatedResultDTO;
use App\Service\User\DTO\UserDTO;

final class UserResponseConverter
{
    public function toResponse(UserDTO $dto): UserResponse
    {
        return new UserResponse(
            id: $dto->id,
            name: $dto->name,
            email: $dto->email,
            created_at: $dto->createdAt->format(\DateTimeInterface::ATOM),
            updated_at: $dto->updatedAt->format(\DateTimeInterface::ATOM),
        );
    }

    public function toPaginatedResponse(PaginatedResultDTO $result): PaginatedResponse
    {
        $data = array_map(fn (UserDTO $dto) => $this->toResponse($dto), $result->items);

        return new PaginatedResponse(
            data: $data,
            meta: [
                'total' => $result->total,
                'page' => $result->page,
                'limit' => $result->limit,
                'total_pages' => $result->totalPages(),
            ],
        );
    }
}
