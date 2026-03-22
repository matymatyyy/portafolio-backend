<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use App\Domain\Project\ProjectId;
use Symfony\Component\HttpFoundation\Response;

final class ProjectNotFoundException extends DomainException
{
    public static function withId(ProjectId $id): self
    {
        return new self(sprintf('Project with ID "%s" was not found.', $id->value()));
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
