<?php

declare(strict_types=1);

namespace App\Domain\Cv\Exception;

use Symfony\Component\HttpFoundation\Response;

final class InvalidCvFormatException extends DomainException
{
    public static function forMimeType(string $mimeType): self
    {
        return new self(sprintf('Invalid CV format "%s". Allowed formats: PDF, DOC, DOCX.', $mimeType));
    }

    public function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function errorCode(): string
    {
        return 'validation_error';
    }
}
