<?php

declare(strict_types=1);

namespace App\Domain\User;

use DateTimeImmutable;

class User
{
    private UserId $id;

    private string $name;

    private Email $email;

    private HashedPassword $password;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    public function __construct(
        UserId $id,
        string $name,
        Email $email,
        HashedPassword $password,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
        $this->updatedAt = $createdAt;
    }

    public static function create(UserId $id, string $name, Email $email, HashedPassword $password): self
    {
        return new self($id, $name, $email, $password, new DateTimeImmutable());
    }

    public static function reconstitute(
        UserId $id,
        string $name,
        Email $email,
        HashedPassword $password,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        $user = new self($id, $name, $email, $password, $createdAt);
        $user->updatedAt = $updatedAt;

        return $user;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateProfile(string $name, Email $email): void
    {
        $this->name = $name;
        $this->email = $email;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updatePassword(HashedPassword $password): void
    {
        $this->password = $password;
        $this->updatedAt = new DateTimeImmutable();
    }
}
