<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

final class InvalidArgumentException extends DomainException
{
    public static function because(string $reason): self
    {
        return new self($reason);
    }
}
