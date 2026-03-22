<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Exception\InvalidEmailException;

final readonly class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $sanitized = trim(strtolower($value));

        if (!filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmailException::withEmail($value);
        }

        $this->value = $sanitized;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
