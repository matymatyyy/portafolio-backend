<?php

declare(strict_types=1);

namespace App\Tests\Service\User;

use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\UserRepositoryInterface;
use App\Service\User\DeleteUserUseCase;
use App\Tests\Domain\User\UserMother;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteUserUseCaseTest extends TestCase
{
    private UserRepositoryInterface&MockObject $userRepository;

    private DeleteUserUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->useCase = new DeleteUserUseCase($this->userRepository);
    }

    #[Test]
    public function itDeletesAUser(): void
    {
        $user = UserMother::create();

        $this->userRepository->expects(self::once())
            ->method('findById')
            ->willReturn($user);

        $this->userRepository->expects(self::once())
            ->method('remove')
            ->with($user);

        $this->useCase->execute('test-id');
    }

    #[Test]
    public function itThrowsWhenUserNotFound(): void
    {
        $this->userRepository->expects(self::once())
            ->method('findById')
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);

        $this->useCase->execute('nonexistent-id');
    }
}
