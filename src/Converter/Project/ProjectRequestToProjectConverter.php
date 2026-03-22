<?php

declare(strict_types=1);

namespace App\Converter\Project;

use App\Model\Request\Project\ProjectRequest;
use App\Service\Project\CreateProjectDTO;
use App\Service\Project\UpdateProjectDTO;

final class ProjectRequestToProjectConverter
{
    public function fromCreateRequest(ProjectRequest $request): CreateProjectDTO
    {
        return new CreateProjectDTO(
            title: $request->title,
            description: $request->description,
            imageUrl: $request->imageUrl,
            projectUrl: $request->projectUrl,
            repoUrl: $request->repoUrl,
            technologies: $request->technologies,
            status: $request->status,
        );
    }

    public function fromUpdateRequest(string $id, ProjectRequest $request): UpdateProjectDTO
    {
        return new UpdateProjectDTO(
            id: $id,
            title: $request->title,
            description: $request->description,
            imageUrl: $request->imageUrl,
            projectUrl: $request->projectUrl,
            repoUrl: $request->repoUrl,
            technologies: $request->technologies,
            status: $request->status,
        );
    }
}
