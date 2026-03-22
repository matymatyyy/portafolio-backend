<?php

declare(strict_types=1);

namespace App\Domain\Visit;

use App\Domain\Visit\Exception\InvalidArgumentException;

final readonly class VisitId
{
    private string $value;

    public function __construct(string $value)
    {
        if ($value === '') {
            throw InvalidArgumentException::because('Visit ID cannot be empty.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function generate(): self
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return new self(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
