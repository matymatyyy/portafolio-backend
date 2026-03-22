<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Domain\User\Email;
use Symfony\Component\HttpFoundation\Response;

final class UserAlreadyExistsException extends DomainException
{
    public static function withEmail(Email $email): self
    {
        return new self(sprintf('User with email "%s" already exists.', $email->value()));
    }

    public function httpStatusCode(): int
    {
        return Response::HTTP_CONFLICT;
    }

    public function errorCode(): string
    {
        return 'conflict';
    }
}
