<?php

declare(strict_types=1);

namespace App\Service\Project\DTO;

final readonly class UpdateProjectDTO
{
    /**
     * @param string[] $technologies
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public ?string $imageUrl,
        public ?string $projectUrl,
        public ?string $repoUrl,
        public array $technologies,
        public string $status,
        public int $sortOrder = 0,
    ) {
    }
}
