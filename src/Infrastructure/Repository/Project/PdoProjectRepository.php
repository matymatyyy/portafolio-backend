<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Project;

use App\Domain\Project\Project;
use App\Domain\Project\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\Slug;
use App\Domain\Project\Status;
use App\Infrastructure\Repository\PaginatedQueryTrait;
use DateTimeImmutable;
use PDO;

final readonly class PdoProjectRepository implements ProjectRepositoryInterface
{
    use PaginatedQueryTrait;

    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function save(Project $project): void
    {
        $sql = <<<'SQL'
            INSERT INTO projects (id, title, slug, description, image_url, project_url, repo_url, technologies, status, sort_order, created_at, updated_at)
            VALUES (:id, :title, :slug, :description, :image_url, :project_url, :repo_url, :technologies, :status, :sort_order, :created_at, :updated_at)
            ON CONFLICT (id) DO UPDATE SET
                title = EXCLUDED.title,
                slug = EXCLUDED.slug,
                description = EXCLUDED.description,
                image_url = EXCLUDED.image_url,
                project_url = EXCLUDED.project_url,
                repo_url = EXCLUDED.repo_url,
                technologies = EXCLUDED.technologies,
                status = EXCLUDED.status,
                sort_order = EXCLUDED.sort_order,
                updated_at = EXCLUDED.updated_at
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $project->id()
                ->value(),
            'title' => $project->title(),
            'slug' => $project->slug()
                ->value(),
            'description' => $project->description(),
            'image_url' => $project->imageUrl(),
            'project_url' => $project->projectUrl(),
            'repo_url' => $project->repoUrl(),
            'technologies' => json_encode($project->technologies(), JSON_THROW_ON_ERROR),
            'status' => $project->status()
                ->value,
            'sort_order' => $project->sortOrder(),
            'created_at' => $project->createdAt()
                ->format('Y-m-d H:i:s'),
            'updated_at' => $project->updatedAt()
                ->format('Y-m-d H:i:s'),
        ]);
    }

    public function remove(Project $project): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM projects WHERE id = :id');
        $stmt->execute([
            'id' => $project->id()
                ->value(),
        ]);
    }

    public function findById(ProjectId $id): ?Project
    {
        $stmt = $this->pdo->prepare('SELECT * FROM projects WHERE id = :id');
        $stmt->execute([
            'id' => $id->value(),
        ]);

        /** @var array<string, string|null>|false $row */
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrateProject($row);
    }

    public function findBySlug(Slug $slug): ?Project
    {
        $stmt = $this->pdo->prepare('SELECT * FROM projects WHERE slug = :slug');
        $stmt->execute([
            'slug' => $slug->value(),
        ]);

        /** @var array<string, string|null>|false $row */
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrateProject($row);
    }

    /**
     * @param array<string, string> $filters
     * @return array{items: Project[], total: int}
     */
    public function findPaginated(int $page, int $limit, array $filters = []): array
    {
        $result = $this->executePaginatedQuery(
            $this->pdo,
            'projects',
            [
                'title' => [
                    'column' => 'title',
                    'operator' => 'LIKE',
                ],
                'status' => [
                    'column' => 'status',
                    'operator' => '=',
                ],
            ],
            $filters,
            $page,
            $limit,
            'sort_order ASC',
        );

        /** @var array<int, array<string, string|null>> $rows */
        $rows = $result['rows'];

        $items = array_map(fn (array $row): Project => $this->hydrateProject($row), $rows);

        return [
            'items' => $items,
            'total' => $result['total'],
        ];
    }

    /**
     * @param array<string, string|null> $row
     */
    private function hydrateProject(array $row): Project
    {
        /** @var string[] $technologies */
        $technologies = json_decode((string) $row['technologies'], true, 512, JSON_THROW_ON_ERROR);

        return Project::reconstitute(
            ProjectId::fromString((string) $row['id']),
            (string) $row['title'],
            Slug::fromString((string) $row['slug']),
            (string) $row['description'],
            $row['image_url'],
            $row['project_url'],
            $row['repo_url'],
            $technologies,
            Status::from((string) $row['status']),
            (int) ($row['sort_order'] ?? 0),
            new DateTimeImmutable((string) $row['created_at']),
            new DateTimeImmutable((string) $row['updated_at']),
        );
    }
}
