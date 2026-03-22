<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use Symfony\Component\HttpFoundation\Response;

final class InvalidEmailException extends DomainException
{
    public static function withEmail(string $email): self
    {
        return new self(sprintf('The email "%s" is not valid.', $email));
    }

    public function httpStatusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    public function errorCode(): string
    {
        return 'validation_error';
    }
}
