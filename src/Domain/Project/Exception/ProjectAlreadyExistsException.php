<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use App\Domain\Project\Slug;
use Symfony\Component\HttpFoundation\Response;

final class ProjectAlreadyExistsException extends DomainException
{
    public static function withSlug(Slug $slug): self
    {
        return new self(sprintf('Project with slug "%s" already exists.', $slug->value()));
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
