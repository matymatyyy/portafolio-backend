<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Converter\User\UserDomainConverter;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Service\User\DTO\UserDTO;

final readonly class GetUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserDomainConverter $userConverter,
    ) {
    }

    public function execute(string $id): UserDTO
    {
        $userId = UserId::fromString($id);
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw UserNotFoundException::withId($userId);
        }

        return $this->userConverter->toDTO($user);
    }
}
