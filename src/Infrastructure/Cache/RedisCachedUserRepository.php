<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Domain\User\Email;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Repository\User\PdoUserRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class RedisCachedUserRepository implements UserRepositoryInterface
{
    private const int TTL = 3600;

    public function __construct(
        #[Autowire(service: PdoUserRepository::class)]
        private UserRepositoryInterface $inner,
        private CacheItemPoolInterface $cache,
    ) {
    }

    public function save(User $user): void
    {
        $this->inner->save($user);

        $this->invalidate($user);
    }

    public function remove(User $user): void
    {
        $this->inner->remove($user);

        $this->invalidate($user);
    }

    public function findById(UserId $id): ?User
    {
        $key = $this->idKey($id->value());

        try {
            $item = $this->cache->getItem($key);

            if ($item->isHit()) {
                /** @var User */
                return $item->get();
            }
        } catch (\Throwable) {
            return $this->inner->findById($id);
        }

        $user = $this->inner->findById($id);

        if ($user !== null) {
            try {
                $item->set($user);
                $item->expiresAfter(self::TTL);
                $this->cache->save($item);
            } catch (\Throwable) {
                // Redis down — ignore, user was already fetched from PDO
            }
        }

        return $user;
    }

    public function findByEmail(Email $email): ?User
    {
        $key = $this->emailKey($email->value());

        try {
            $item = $this->cache->getItem($key);

            if ($item->isHit()) {
                /** @var User */
                return $item->get();
            }
        } catch (\Throwable) {
            return $this->inner->findByEmail($email);
        }

        $user = $this->inner->findByEmail($email);

        if ($user !== null) {
            try {
                $item->set($user);
                $item->expiresAfter(self::TTL);
                $this->cache->save($item);
            } catch (\Throwable) {
                // Redis down — ignore
            }
        }

        return $user;
    }

    /**
     * @param array<string, string> $filters
     * @return array{items: User[], total: int}
     */
    public function findPaginated(int $page, int $limit, array $filters = []): array
    {
        return $this->inner->findPaginated($page, $limit, $filters);
    }

    private function invalidate(User $user): void
    {
        try {
            $this->cache->deleteItems([
                $this->idKey($user->id()->value()),
                $this->emailKey($user->email()->value()),
            ]);
        } catch (\Throwable) {
            // Redis down — ignore
        }
    }

    private function idKey(string $id): string
    {
        return 'user_id_' . $id;
    }

    private function emailKey(string $email): string
    {
        return 'user_email_' . str_replace('@', '_at_', $email);
    }
}
