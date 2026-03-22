<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\User;

use App\Domain\User\Email;
use App\Domain\User\HashedPassword;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Repository\PaginatedQueryTrait;
use DateTimeImmutable;
use PDO;

final readonly class PdoUserRepository implements UserRepositoryInterface
{
    use PaginatedQueryTrait;

    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function save(User $user): void
    {
        $sql = <<<'SQL'
            INSERT INTO users (id, name, email, password_hash, created_at, updated_at)
            VALUES (:id, :name, :email, :password_hash, :created_at, :updated_at)
            ON CONFLICT (id) DO UPDATE SET
                name = EXCLUDED.name,
                email = EXCLUDED.email,
                password_hash = EXCLUDED.password_hash,
                updated_at = EXCLUDED.updated_at
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $user->id()
                ->value(),
            'name' => $user->name(),
            'email' => $user->email()
                ->value(),
            'password_hash' => $user->password()
                ->value(),
            'created_at' => $user->createdAt()
                ->format('Y-m-d H:i:s'),
            'updated_at' => $user->updatedAt()
                ->format('Y-m-d H:i:s'),
        ]);
    }

    public function remove(User $user): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute([
            'id' => $user->id()
                ->value(),
        ]);
    }

    public function findById(UserId $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([
            'id' => $id->value(),
        ]);

        /** @var array<string, string>|false $row */
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrateUser($row);
    }

    public function findByEmail(Email $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([
            'email' => $email->value(),
        ]);

        /** @var array<string, string>|false $row */
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrateUser($row);
    }

    /**
     * @param array<string, string> $filters
     * @return array{items: User[], total: int}
     */
    public function findPaginated(int $page, int $limit, array $filters = []): array
    {
        $result = $this->executePaginatedQuery(
            $this->pdo,
            'users',
            [
                'email' => [
                    'column' => 'email',
                    'operator' => 'LIKE',
                ],
                'name' => [
                    'column' => 'name',
                    'operator' => 'LIKE',
                ],
            ],
            $filters,
            $page,
            $limit,
        );

        /** @var array<int, array<string, string>> $rows */
        $rows = $result['rows'];

        $items = array_map(fn (array $row): User => $this->hydrateUser($row), $rows);

        return [
            'items' => $items,
            'total' => $result['total'],
        ];
    }

    /**
     * @param array<string, string> $row
     */
    private function hydrateUser(array $row): User
    {
        return User::reconstitute(
            UserId::fromString($row['id']),
            $row['name'],
            new Email($row['email']),
            HashedPassword::fromHash($row['password_hash']),
            new DateTimeImmutable($row['created_at']),
            new DateTimeImmutable($row['updated_at']),
        );
    }
}
