<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use App\Domain\Common\DomainExceptionInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

abstract class DomainException extends RuntimeException implements DomainExceptionInterface
{
    public function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function errorCode(): string
    {
        return 'domain_error';
    }
}
