<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Converter\Project\ProjectDomainConverter;
use App\Domain\Project\Exception\ProjectNotFoundException;
use App\Domain\Project\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Service\Project\DTO\ProjectDTO;

final readonly class GetProjectUseCase
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private ProjectDomainConverter $projectConverter,
    ) {
    }

    public function execute(string $id): ProjectDTO
    {
        $projectId = ProjectId::fromString($id);
        $project = $this->projectRepository->findById($projectId);

        if ($project === null) {
            throw ProjectNotFoundException::withId($projectId);
        }

        return $this->projectConverter->toDTO($project);
    }
}
