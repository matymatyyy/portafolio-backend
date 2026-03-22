<?php

declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\Email;
use App\Domain\User\HashedPassword;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itCreatesAUser(): void
    {
        $user = UserMother::create();

        self::shouldBeSame('test-id', $user->id()->value());
        self::shouldBeSame('John Doe', $user->name());
        self::shouldBeSame('john@example.com', $user->email()->value());
        self::shouldBeSame('hashed_password', $user->password()->value());
        self::shouldNotBeNull($user->createdAt());
        self::shouldNotBeNull($user->updatedAt());
    }

    #[Test]
    public function itUpdatesProfile(): void
    {
        $user = UserMother::create();
        $originalUpdatedAt = $user->updatedAt();

        $user->updateProfile('Jane Doe', new Email('jane@example.com'));

        self::shouldBeSame('Jane Doe', $user->name());
        self::shouldBeSame('jane@example.com', $user->email()->value());
        self::shouldBeGreaterThanOrEqualTo($originalUpdatedAt, $user->updatedAt());
    }

    #[Test]
    public function itUpdatesPassword(): void
    {
        $user = UserMother::create();

        $user->updatePassword(HashedPassword::fromHash('new_hashed_password'));

        self::shouldBeSame('new_hashed_password', $user->password()->value());
    }
}
