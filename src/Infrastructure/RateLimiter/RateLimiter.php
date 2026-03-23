<?php

declare(strict_types=1);

namespace App\Infrastructure\RateLimiter;

use Psr\Cache\CacheItemPoolInterface;

final readonly class RateLimiter
{
    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * Returns true if the request is allowed, false if rate limited.
     *
     * @param bool $failOpen If true, allows requests when cache is unavailable. If false, denies them.
     */
    public function isAllowed(string $key, int $maxAttempts, int $windowSeconds, bool $failOpen = true): bool
    {
        try {
            $cacheKey = 'rate_limit_' . str_replace(['@', ':', '.'], '_', $key);
            $item = $this->cache->getItem($cacheKey);

            if (!$item->isHit()) {
                $item->set(1);
                $item->expiresAfter($windowSeconds);
                $this->cache->save($item);

                return true;
            }

            /** @var int $count */
            $count = $item->get();

            if ($count >= $maxAttempts) {
                return false;
            }

            $item->set($count + 1);
            $this->cache->save($item);

            return true;
        } catch (\Throwable) {
            return $failOpen;
        }
    }
}
