<?php

declare(strict_types=1);

namespace App\Domain\Contact\Exception;

use Symfony\Component\HttpFoundation\Response;

final class ContactEmailFailedException extends DomainException
{
    public static function because(string $reason): self
    {
        return new self(sprintf('Failed to send contact email: %s', $reason));
    }

    public function httpStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function errorCode(): string
    {
        return 'email_error';
    }
}
