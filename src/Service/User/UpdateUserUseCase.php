<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Converter\User\UserDomainConverter;
use App\Domain\User\Email;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\HashedPassword;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Service\User\DTO\UpdateUserDTO;
use App\Service\User\DTO\UserDTO;

final readonly class UpdateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private UserDomainConverter $userConverter,
    ) {
    }

    public function execute(UpdateUserDTO $dto): UserDTO
    {
        $userId = UserId::fromString($dto->id);
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw UserNotFoundException::withId($userId);
        }

        $newEmail = new Email($dto->email);

        if (!$user->email()->equals($newEmail)) {
            $existingUser = $this->userRepository->findByEmail($newEmail);

            if ($existingUser !== null) {
                throw UserAlreadyExistsException::withEmail($newEmail);
            }
        }

        $user->updateProfile($dto->name, $newEmail);

        if ($dto->plainPassword !== null) {
            $user->updatePassword(HashedPassword::fromHash($this->passwordHasher->hash($dto->plainPassword)));
        }

        $this->userRepository->save($user);

        return $this->userConverter->toDTO($user);
    }
}
