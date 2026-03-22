<?php

declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\Email;
use App\Domain\User\HashedPassword;
use App\Domain\User\User;
use App\Domain\User\UserId;

final class UserMother
{
    public static function create(
        string $id = 'test-id',
        string $name = 'John Doe',
        string $email = 'john@example.com',
        string $password = 'hashed_password',
    ): User {
        return User::create(
            UserId::fromString($id),
            $name,
            new Email($email),
            HashedPassword::fromHash($password),
        );
    }
}
