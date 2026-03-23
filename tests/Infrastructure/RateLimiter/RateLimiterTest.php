<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\RateLimiter;

use App\Infrastructure\RateLimiter\RateLimiter;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

final class RateLimiterTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itAllowsFirstRequest(): void
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);
        $item->expects(self::once())->method('set')->with(1);
        $item->expects(self::once())->method('expiresAfter')->with(60);

        $cache = $this->createMock(CacheItemPoolInterface::class);
        $cache->method('getItem')->willReturn($item);
        $cache->expects(self::once())->method('save')->with($item);

        $limiter = new RateLimiter($cache);

        self::shouldBeTrue($limiter->isAllowed('test_key', 5, 60));
    }

    #[Test]
    public function itAllowsRequestsUnderLimit(): void
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn(3);
        $item->expects(self::once())->method('set')->with(4);

        $cache = $this->createMock(CacheItemPoolInterface::class);
        $cache->method('getItem')->willReturn($item);
        $cache->expects(self::once())->method('save');

        $limiter = new RateLimiter($cache);

        self::shouldBeTrue($limiter->isAllowed('test_key', 5, 60));
    }

    #[Test]
    public function itBlocksRequestsAtLimit(): void
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn(5);

        $cache = $this->createMock(CacheItemPoolInterface::class);
        $cache->method('getItem')->willReturn($item);

        $limiter = new RateLimiter($cache);

        self::shouldBeFalse($limiter->isAllowed('test_key', 5, 60));
    }

    #[Test]
    public function itFailsOpenByDefault(): void
    {
        $cache = $this->createMock(CacheItemPoolInterface::class);
        $cache->method('getItem')->willThrowException(new \RuntimeException('Redis down'));

        $limiter = new RateLimiter($cache);

        self::shouldBeTrue($limiter->isAllowed('test_key', 5, 60));
    }

    #[Test]
    public function itFailsClosedWhenConfigured(): void
    {
        $cache = $this->createMock(CacheItemPoolInterface::class);
        $cache->method('getItem')->willThrowException(new \RuntimeException('Redis down'));

        $limiter = new RateLimiter($cache);

        self::shouldBeFalse($limiter->isAllowed('test_key', 5, 60, failOpen: false));
    }
}
