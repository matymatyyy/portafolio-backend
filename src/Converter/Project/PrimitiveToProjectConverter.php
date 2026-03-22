<?php

declare(strict_types=1);

namespace App\Converter\Project;

use App\Domain\Project\Project;
use App\Service\Project\ProjectDTO;

final class PrimitiveToProjectConverter
{
    public function toDTO(Project $project): ProjectDTO
    {
        return new ProjectDTO(
            id: $project->id()
                ->value(),
            title: $project->title(),
            slug: $project->slug()
                ->value(),
            description: $project->description(),
            imageUrl: $project->imageUrl(),
            projectUrl: $project->projectUrl(),
            repoUrl: $project->repoUrl(),
            technologies: $project->technologies(),
            status: $project->status()
                ->value,
            createdAt: $project->createdAt(),
            updatedAt: $project->updatedAt(),
        );
    }
}
