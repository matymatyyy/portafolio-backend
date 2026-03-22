<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Domain\User\UserId;
use Symfony\Component\HttpFoundation\Response;

final class UserNotFoundException extends DomainException
{
    public static function withId(UserId $id): self
    {
        return new self(sprintf('User with ID "%s" was not found.', $id->value()));
    }

    public function httpStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function errorCode(): string
    {
        return 'not_found';
    }
}
