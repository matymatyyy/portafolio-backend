<?php

declare(strict_types=1);

namespace App\Converter\Project;

use App\Model\Request\Project\UpdateProjectRequest;
use App\Service\Project\DTO\UpdateProjectDTO;

final class UpdateProjectRequestConverter
{
    public function fromRequest(string $id, UpdateProjectRequest $request): UpdateProjectDTO
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
            sortOrder: $request->sortOrder,
        );
    }
}
