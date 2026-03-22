<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;

final readonly class DeleteUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function execute(string $id): void
    {
        $userId = UserId::fromString($id);
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw UserNotFoundException::withId($userId);
        }

        $this->userRepository->remove($user);
    }
}
