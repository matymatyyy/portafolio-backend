<?php

declare(strict_types=1);

namespace App\Tests\Service\User;

use App\Converter\User\UserDomainConverter;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\UserRepositoryInterface;
use App\Service\User\GetUserUseCase;
use App\Tests\Domain\User\UserMother;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetUserUseCaseTest extends TestCase
{
    use Assertions;

    private UserRepositoryInterface&MockObject $userRepository;

    private GetUserUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->useCase = new GetUserUseCase($this->userRepository, new UserDomainConverter());
    }

    #[Test]
    public function itReturnsAUserById(): void
    {
        $user = UserMother::create();

        $this->userRepository->expects(self::once())
            ->method('findById')
            ->willReturn($user);

        $result = $this->useCase->execute('test-id');

        self::shouldBeSame('test-id', $result->id);
        self::shouldBeSame('John Doe', $result->name);
        self::shouldBeSame('john@example.com', $result->email);
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
