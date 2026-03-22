<?php

declare(strict_types=1);

namespace App\Domain\Project;

interface ProjectRepository
{
    public function save(Project $project): void;

    public function remove(Project $project): void;

    public function findById(ProjectId $id): ?Project;

    public function findBySlug(Slug $slug): ?Project;

    /**
     * @param array<string, string> $filters
     * @return array{items: Project[], total: int}
     */
    public function findPaginated(int $page, int $limit, array $filters = []): array;
}
