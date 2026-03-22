<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Exception\InvalidArgumentException;

final readonly class HashedPassword
{
    private string $value;

    private function __construct(string $hashedValue)
    {
        if ($hashedValue === '') {
            throw InvalidArgumentException::because('Hashed password cannot be empty.');
        }

        $this->value = $hashedValue;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public function value(): string
    {
        return $this->value;
    }
}
