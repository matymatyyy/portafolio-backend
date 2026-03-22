<?php

declare(strict_types=1);

namespace App\Service\Visit\DTO;

final readonly class VisitStatsDTO
{
    /**
     * @param array<string, int>                            $visitsByPage
     * @param array<int, array{date: string, count: int}>   $visitsByDay
     * @param array<int, array{referrer: string, count: int}> $topReferrers
     */
    public function __construct(
        public int $totalVisits,
        public int $uniqueVisitors,
        public array $visitsByPage,
        public array $visitsByDay,
        public array $topReferrers,
    ) {
    }
}
