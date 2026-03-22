<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    public function save(User $user): void;

    public function remove(User $user): void;

    public function findById(UserId $id): ?User;

    public function findByEmail(Email $email): ?User;

    /**
     * @param array<string, string> $filters
     * @return array{items: User[], total: int}
     */
    public function findPaginated(int $page, int $limit, array $filters = []): array;
}
