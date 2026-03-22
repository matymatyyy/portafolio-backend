<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Converter\User\UserDomainConverter;
use App\Domain\User\Email;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\HashedPassword;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Service\User\DTO\CreateUserDTO;
use App\Service\User\DTO\UserDTO;
use App\Service\User\Message\SendWelcomeEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private UserDomainConverter $userConverter,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function execute(CreateUserDTO $dto): UserDTO
    {
        $email = new Email($dto->email);

        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser !== null) {
            throw UserAlreadyExistsException::withEmail($email);
        }

        $user = User::create(
            UserId::generate(),
            $dto->name,
            $email,
            HashedPassword::fromHash($this->passwordHasher->hash($dto->plainPassword)),
        );

        $this->userRepository->save($user);

        $this->messageBus->dispatch(new SendWelcomeEmailMessage($dto->email, $dto->name));

        return $this->userConverter->toDTO($user);
    }
}
