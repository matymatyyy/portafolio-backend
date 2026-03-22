<?php

declare(strict_types=1);

namespace App\Domain\Cv\Exception;

use Symfony\Component\HttpFoundation\Response;

final class CvNotFoundException extends DomainException
{
    public static function noActiveCv(): self
    {
        return new self('No active CV was found.');
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
