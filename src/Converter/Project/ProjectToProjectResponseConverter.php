<?php

declare(strict_types=1);

namespace App\Converter\Project;

use App\Model\Response\Project\PaginatedProjectResponse;
use App\Model\Response\Project\ProjectResponse;
use App\Service\Project\PaginatedProjectResultDTO;
use App\Service\Project\ProjectDTO;

final class ProjectToProjectResponseConverter
{
    public function toResponse(ProjectDTO $dto): ProjectResponse
    {
        return new ProjectResponse(
            id: $dto->id,
            title: $dto->title,
            slug: $dto->slug,
            description: $dto->description,
            image_url: $dto->imageUrl,
            project_url: $dto->projectUrl,
            repo_url: $dto->repoUrl,
            technologies: $dto->technologies,
            status: $dto->status,
            created_at: $dto->createdAt->format(\DateTimeInterface::ATOM),
            updated_at: $dto->updatedAt->format(\DateTimeInterface::ATOM),
        );
    }

    public function toPaginatedResponse(PaginatedProjectResultDTO $result): PaginatedProjectResponse
    {
        $data = array_map(fn (ProjectDTO $dto) => $this->toResponse($dto), $result->items);

        return new PaginatedProjectResponse(
            data: $data,
            meta: [
                'total' => $result->total,
                'page' => $result->page,
                'limit' => $result->limit,
                'total_pages' => $result->totalPages(),
            ],
        );
    }
}
