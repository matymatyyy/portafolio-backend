<?php

declare(strict_types=1);

namespace App\Tests\Service\User;

use App\Converter\User\UserDomainConverter;
use App\Domain\User\Email;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Service\User\CreateUserUseCase;
use App\Service\User\DTO\CreateUserDTO;
use App\Service\User\Message\SendWelcomeEmailMessage;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateUserUseCaseTest extends TestCase
{
    use Assertions;

    private UserRepositoryInterface&MockObject $userRepository;

    private PasswordHasherInterface&MockObject $passwordHasher;

    private MessageBusInterface&MockObject $messageBus;

    private CreateUserUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(PasswordHasherInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);

        $this->useCase = new CreateUserUseCase(
            $this->userRepository,
            $this->passwordHasher,
            new UserDomainConverter(),
            $this->messageBus,
        );
    }

    #[Test]
    public function itCreatesAUserSuccessfully(): void
    {
        $dto = new CreateUserDTO('John Doe', 'john@example.com', 'password123');

        $this->userRepository->expects(self::once())
            ->method('findByEmail')
            ->willReturn(null);

        $this->passwordHasher->expects(self::once())
            ->method('hash')
            ->with('password123')
            ->willReturn('hashed_password');

        $this->userRepository->expects(self::once())
            ->method('save');

        $this->messageBus->expects(self::once())
            ->method('dispatch')
            ->with(self::callback(
                static fn (SendWelcomeEmailMessage $msg) => $msg->email === 'john@example.com' && $msg->name === 'John Doe',
            ))
            ->willReturn(new Envelope(new \stdClass()));

        $result = $this->useCase->execute($dto);

        self::shouldBeSame('John Doe', $result->name);
        self::shouldBeSame('john@example.com', $result->email);
        self::shouldNotBeEmpty($result->id);
    }

    #[Test]
    public function itThrowsWhenEmailAlreadyExists(): void
    {
        $dto = new CreateUserDTO('John Doe', 'john@example.com', 'password123');

        $existingUser = $this->createMock(User::class);

        $this->userRepository->expects(self::once())
            ->method('findByEmail')
            ->with(self::callback(static fn (Email $email) => $email->value() === 'john@example.com'))
            ->willReturn($existingUser);

        $this->messageBus->expects(self::never())
            ->method('dispatch');

        $this->expectException(UserAlreadyExistsException::class);

        $this->useCase->execute($dto);
    }
}
