<?php

declare(strict_types=1);

namespace App\Domain\Common\Exception;

use Symfony\Component\HttpFoundation\Response;

final class FileStorageException extends DomainException
{
    public static function uploadFailed(string $reason): self
    {
        return new self(sprintf('File upload failed: %s', $reason));
    }

    public static function deleteFailed(string $key, string $reason): self
    {
        return new self(sprintf('Failed to delete file "%s": %s', $key, $reason));
    }

    public function httpStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function errorCode(): string
    {
        return 'file_storage_error';
    }
}
