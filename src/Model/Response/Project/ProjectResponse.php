<?php

declare(strict_types=1);

namespace App\Model\Response\Project;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ProjectResponse', required: [
    'id',
    'title',
    'slug',
    'description',
    'technologies',
    'status',
    'created_at',
    'updated_at',
])]
final readonly class ProjectResponse
{
    /**
     * @param string[] $technologies
     */
    public function __construct(
        #[OA\Property(example: '550e8400-e29b-41d4-a716-446655440000')]
        public string $id,
        #[OA\Property(example: 'My Portfolio Website')]
        public string $title,
        #[OA\Property(example: 'my-portfolio-website')]
        public string $slug,
        #[OA\Property(example: 'A personal portfolio built with PHP and Symfony')]
        public string $description,
        #[OA\Property(example: 'https://example.com/image.png', nullable: true)]
        public ?string $image_url,
        #[OA\Property(example: 'https://example.com', nullable: true)]
        public ?string $project_url,
        #[OA\Property(example: 'https://github.com/user/repo', nullable: true)]
        public ?string $repo_url,
        #[OA\Property(type: 'array', items: new OA\Items(type: 'string'), example: ['PHP', 'Symfony', 'PostgreSQL'])]
        public array $technologies,
        #[OA\Property(example: 'active')]
        public string $status,
        #[OA\Property(example: '2024-01-01T00:00:00+00:00')]
        public string $created_at,
        #[OA\Property(example: '2024-01-01T00:00:00+00:00')]
        public string $updated_at,
    ) {
    }
}
