<?php

declare(strict_types=1);

namespace App\Service\Project;

use DateTimeImmutable;

final readonly class ProjectDTO
{
    /**
     * @param string[] $technologies
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $slug,
        public string $description,
        public ?string $imageUrl,
        public ?string $projectUrl,
        public ?string $repoUrl,
        public array $technologies,
        public string $status,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
