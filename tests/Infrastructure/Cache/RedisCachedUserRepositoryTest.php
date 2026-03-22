<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Cache;

use App\Domain\User\Email;
use App\Domain\User\HashedPassword;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Cache\RedisCachedUserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

final class RedisCachedUserRepositoryTest extends TestCase
{
    private UserRepositoryInterface&MockObject $inner;

    private CacheItemPoolInterface&MockObject $cache;

    private RedisCachedUserRepository $repository;

    protected function setUp(): void
    {
        $this->inner = $this->createMock(UserRepositoryInterface::class);
        $this->cache = $this->createMock(CacheItemPoolInterface::class);
        $this->repository = new RedisCachedUserRepository($this->inner, $this->cache);
    }

    #[Test]
    public function findByIdReturnsCachedUser(): void
    {
        $user = $this->createUser();
        $item = $this->createCacheItem(isHit: true, value: $user);

        $this->cache->expects(self::once())
            ->method('getItem')
            ->willReturn($item);

        $this->inner->expects(self::never())
            ->method('findById');

        $result = $this->repository->findById(UserId::fromString('test-id'));

        self::assertSame($user, $result);
    }

    #[Test]
    public function findByIdQueriesPdoOnCacheMiss(): void
    {
        $user = $this->createUser();
        $item = $this->createCacheItem(isHit: false);

        $this->cache->expects(self::once())
            ->method('getItem')
            ->willReturn($item);

        $this->inner->expects(self::once())
            ->method('findById')
            ->willReturn($user);

        $item->expects(self::once())->method('set')->with($user);
        $item->expects(self::once())->method('expiresAfter')->with(3600);

        $this->cache->expects(self::once())
            ->method('save')
            ->with($item);

        $result = $this->repository->findById(UserId::fromString('test-id'));

        self::assertSame($user, $result);
    }

    #[Test]
    public function findByIdFallsToPdoOnRedisFailure(): void
    {
        $user = $this->createUser();

        $this->cache->expects(self::once())
            ->method('getItem')
            ->willThrowException(new \RuntimeException('Redis down'));

        $this->inner->expects(self::once())
            ->method('findById')
            ->willReturn($user);

        $result = $this->repository->findById(UserId::fromString('test-id'));

        self::assertSame($user, $result);
    }

    #[Test]
    public function findByEmailReturnsCachedUser(): void
    {
        $user = $this->createUser();
        $item = $this->createCacheItem(isHit: true, value: $user);

        $this->cache->expects(self::once())
            ->method('getItem')
            ->willReturn($item);

        $this->inner->expects(self::never())
            ->method('findByEmail');

        $result = $this->repository->findByEmail(new Email('test@example.com'));

        self::assertSame($user, $result);
    }

    #[Test]
    public function findByEmailQueriesPdoOnCacheMiss(): void
    {
        $user = $this->createUser();
        $item = $this->createCacheItem(isHit: false);

        $this->cache->expects(self::once())
            ->method('getItem')
            ->willReturn($item);

        $this->inner->expects(self::once())
            ->method('findByEmail')
            ->willReturn($user);

        $item->expects(self::once())->method('set')->with($user);

        $this->cache->expects(self::once())
            ->method('save')
            ->with($item);

        $result = $this->repository->findByEmail(new Email('test@example.com'));

        self::assertSame($user, $result);
    }

    #[Test]
    public function findByEmailFallsToPdoOnRedisFailure(): void
    {
        $user = $this->createUser();

        $this->cache->expects(self::once())
            ->method('getItem')
            ->willThrowException(new \RuntimeException('Redis down'));

        $this->inner->expects(self::once())
            ->method('findByEmail')
            ->willReturn($user);

        $result = $this->repository->findByEmail(new Email('test@example.com'));

        self::assertSame($user, $result);
    }

    #[Test]
    public function saveDelegatesToPdoAndInvalidatesCache(): void
    {
        $user = $this->createUser();

        $this->inner->expects(self::once())
            ->method('save')
            ->with($user);

        $this->cache->expects(self::once())
            ->method('deleteItems');

        $this->repository->save($user);
    }

    #[Test]
    public function removeDelegatesToPdoAndInvalidatesCache(): void
    {
        $user = $this->createUser();

        $this->inner->expects(self::once())
            ->method('remove')
            ->with($user);

        $this->cache->expects(self::once())
            ->method('deleteItems');

        $this->repository->remove($user);
    }

    #[Test]
    public function findPaginatedAlwaysDelegatesToPdo(): void
    {
        $expected = [
            'items' => [],
            'total' => 0,
        ];

        $this->inner->expects(self::once())
            ->method('findPaginated')
            ->with(1, 10, [])
            ->willReturn($expected);

        $this->cache->expects(self::never())
            ->method('getItem');

        $result = $this->repository->findPaginated(1, 10);

        self::assertSame($expected, $result);
    }

    private function createUser(): User
    {
        return User::create(
            UserId::fromString('test-id'),
            'Test User',
            new Email('test@example.com'),
            HashedPassword::fromHash('$2y$13$test_hash'),
        );
    }

    private function createCacheItem(bool $isHit, ?User $value = null): CacheItemInterface&MockObject
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')
            ->willReturn($isHit);

        if ($isHit && $value !== null) {
            $item->method('get')
                ->willReturn($value);
        }

        return $item;
    }
}
