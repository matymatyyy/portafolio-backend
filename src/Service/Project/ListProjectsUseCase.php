<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Converter\Project\ProjectDomainConverter;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Service\Project\DTO\PaginatedProjectResultDTO;

final readonly class ListProjectsUseCase
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private ProjectDomainConverter $projectConverter,
    ) {
    }

    /**
     * @param array<string, string> $filters
     */
    public function execute(int $page, int $limit, array $filters = []): PaginatedProjectResultDTO
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);

        $result = $this->projectRepository->findPaginated($page, $limit, $filters);

        $items = array_map(fn ($project) => $this->projectConverter->toDTO($project), $result['items']);

        return new PaginatedProjectResultDTO($items, $result['total'], $page, $limit);
    }
}
