<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Converter\User\UserDomainConverter;
use App\Domain\User\UserRepositoryInterface;
use App\Service\User\DTO\PaginatedResultDTO;

final readonly class ListUsersUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserDomainConverter $userConverter,
    ) {
    }

    /**
     * @param array<string, string> $filters
     */
    public function execute(int $page, int $limit, array $filters = []): PaginatedResultDTO
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);

        $result = $this->userRepository->findPaginated($page, $limit, $filters);

        $items = array_map(fn ($user) => $this->userConverter->toDTO($user), $result['items']);

        return new PaginatedResultDTO($items, $result['total'], $page, $limit);
    }
}
