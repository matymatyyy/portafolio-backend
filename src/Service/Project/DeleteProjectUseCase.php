<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Domain\Common\FileStorageInterface;
use App\Domain\Project\Exception\ProjectNotFoundException;
use App\Domain\Project\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;

final readonly class DeleteProjectUseCase
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private FileStorageInterface $fileStorage,
    ) {
    }

    public function execute(string $id): void
    {
        $projectId = ProjectId::fromString($id);
        $project = $this->projectRepository->findById($projectId);

        if ($project === null) {
            throw ProjectNotFoundException::withId($projectId);
        }

        $imageUrl = $project->imageUrl();

        $this->projectRepository->remove($project);

        if ($imageUrl !== null) {
            $this->tryDeleteImage($imageUrl);
        }
    }

    private function tryDeleteImage(string $imageUrl): void
    {
        try {
            $path = parse_url($imageUrl, PHP_URL_PATH);

            if (is_string($path) && $path !== '') {
                $this->fileStorage->delete(ltrim($path, '/'));
            }
        } catch (\Throwable) {
        }
    }
}
