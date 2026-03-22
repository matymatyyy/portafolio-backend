<?php

declare(strict_types=1);

namespace App\Tests\Domain\Project;

use App\Domain\Project\Project;
use App\Domain\Project\ProjectId;
use App\Domain\Project\Slug;
use App\Domain\Project\Status;

final class ProjectMother
{
    public static function create(
        string $id = 'test-id',
        string $title = 'My Project',
        string $description = 'A description',
        ?string $imageUrl = null,
        ?string $projectUrl = null,
        ?string $repoUrl = null,
        array $technologies = ['PHP'],
        Status $status = Status::Active,
    ): Project {
        return Project::create(
            ProjectId::fromString($id),
            $title,
            Slug::fromTitle($title),
            $description,
            $imageUrl,
            $projectUrl,
            $repoUrl,
            $technologies,
            $status,
        );
    }
}
