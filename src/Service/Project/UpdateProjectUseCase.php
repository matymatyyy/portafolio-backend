<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Converter\Project\ProjectDomainConverter;
use App\Domain\Common\FileStorageInterface;
use App\Domain\Project\Exception\ProjectAlreadyExistsException;
use App\Domain\Project\Exception\ProjectNotFoundException;
use App\Domain\Project\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\Slug;
use App\Domain\Project\Status;
use App\Service\Project\DTO\ProjectDTO;
use App\Service\Project\DTO\UpdateProjectDTO;

final readonly class UpdateProjectUseCase
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private ProjectDomainConverter $projectConverter,
        private FileStorageInterface $fileStorage,
    ) {
    }

    public function execute(UpdateProjectDTO $dto): ProjectDTO
    {
        $projectId = ProjectId::fromString($dto->id);
        $project = $this->projectRepository->findById($projectId);

        if ($project === null) {
            throw ProjectNotFoundException::withId($projectId);
        }

        $oldImageUrl = $project->imageUrl();

        $newSlug = Slug::fromTitle($dto->title);

        if (!$project->slug()->equals($newSlug)) {
            $existingProject = $this->projectRepository->findBySlug($newSlug);

            if ($existingProject !== null) {
                throw ProjectAlreadyExistsException::withSlug($newSlug);
            }
        }

        $project->update(
            $dto->title,
            $newSlug,
            $dto->description,
            $dto->imageUrl,
            $dto->projectUrl,
            $dto->repoUrl,
            $dto->technologies,
            Status::from($dto->status),
        );

        $this->projectRepository->save($project);

        if ($oldImageUrl !== null && $oldImageUrl !== $dto->imageUrl) {
            $this->tryDeleteImage($oldImageUrl);
        }

        return $this->projectConverter->toDTO($project);
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
