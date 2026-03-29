<?php

declare(strict_types=1);

namespace App\Domain\Common\Exception;

use Symfony\Component\HttpFoundation\Response;

final class InvalidFileException extends DomainException
{
    public static function forMimeType(string $mimeType): self
    {
        return new self(sprintf('Invalid image format "%s". Allowed formats: JPEG, PNG, GIF, WebP.', $mimeType));
    }

    public static function forFileSize(int $size, int $maxSize): self
    {
        return new self(sprintf('File size %d bytes exceeds maximum allowed %d bytes.', $size, $maxSize));
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
