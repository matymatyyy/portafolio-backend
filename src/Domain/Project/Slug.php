<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\Exception\InvalidArgumentException;

final readonly class Slug
{
    private string $value;

    public function __construct(string $value)
    {
        if ($value === '') {
            throw InvalidArgumentException::because('Slug cannot be empty.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromTitle(string $title): self
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = (string) preg_replace('/[\s-]+/', '-', (string) $slug);
        $slug = trim($slug, '-');

        return new self($slug);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
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
