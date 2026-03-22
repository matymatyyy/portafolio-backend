<?php

declare(strict_types=1);

namespace App\Domain\Visit;

interface VisitRepositoryInterface
{
    public function save(Visit $visit): void;

    public function countTotal(?\DateTimeImmutable $since = null): int;

    public function countUniqueVisitors(?\DateTimeImmutable $since = null): int;

    /**
     * @return array<string, int>
     */
    public function countByPage(?\DateTimeImmutable $since = null): array;

    /**
     * @return array<int, array{date: string, count: int}>
     */
    public function countByDay(int $days): array;

    /**
     * @return array<int, array{referrer: string, count: int}>
     */
    public function countTopReferrers(int $limit): array;
}
