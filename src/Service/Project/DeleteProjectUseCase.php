<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Domain\Project\Exception\ProjectNotFoundException;
use App\Domain\Project\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;

final readonly class DeleteProjectUseCase
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
    ) {
    }

    public function execute(string $id): void
    {
        $projectId = ProjectId::fromString($id);
        $project = $this->projectRepository->findById($projectId);

        if ($project === null) {
            throw ProjectNotFoundException::withId($projectId);
        }

        $this->projectRepository->remove($project);
    }
}
