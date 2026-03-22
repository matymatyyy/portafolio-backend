<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Converter\Project\ProjectDomainConverter;
use App\Domain\Project\Exception\ProjectAlreadyExistsException;
use App\Domain\Project\Project;
use App\Domain\Project\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\Slug;
use App\Domain\Project\Status;
use App\Service\Project\DTO\CreateProjectDTO;
use App\Service\Project\DTO\ProjectDTO;

final readonly class CreateProjectUseCase
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private ProjectDomainConverter $projectConverter,
    ) {
    }

    public function execute(CreateProjectDTO $dto): ProjectDTO
    {
        $slug = Slug::fromTitle($dto->title);

        $existingProject = $this->projectRepository->findBySlug($slug);

        if ($existingProject !== null) {
            throw ProjectAlreadyExistsException::withSlug($slug);
        }

        $project = Project::create(
            ProjectId::generate(),
            $dto->title,
            $slug,
            $dto->description,
            $dto->imageUrl,
            $dto->projectUrl,
            $dto->repoUrl,
            $dto->technologies,
            Status::from($dto->status),
        );

        $this->projectRepository->save($project);

        return $this->projectConverter->toDTO($project);
    }
}
