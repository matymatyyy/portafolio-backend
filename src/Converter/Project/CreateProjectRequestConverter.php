<?php

declare(strict_types=1);

namespace App\Converter\Project;

use App\Model\Request\Project\CreateProjectRequest;
use App\Service\Project\DTO\CreateProjectDTO;

final class CreateProjectRequestConverter
{
    public function fromRequest(CreateProjectRequest $request): CreateProjectDTO
    {
        return new CreateProjectDTO(
            title: $request->title,
            description: $request->description,
            imageUrl: $request->imageUrl,
            projectUrl: $request->projectUrl,
            repoUrl: $request->repoUrl,
            technologies: $request->technologies,
            status: $request->status,
            sortOrder: $request->sortOrder,
        );
    }
}
