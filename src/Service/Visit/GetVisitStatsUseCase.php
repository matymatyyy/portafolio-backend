<?php

declare(strict_types=1);

namespace App\Service\Visit;

use App\Domain\Visit\VisitRepositoryInterface;
use App\Service\Visit\DTO\VisitStatsDTO;
use DateTimeImmutable;

final readonly class GetVisitStatsUseCase
{
    public function __construct(
        private VisitRepositoryInterface $visitRepository,
    ) {
    }

    public function execute(int $days = 30, int $topReferrersLimit = 10): VisitStatsDTO
    {
        $days = max(1, min(365, $days));

        $since = (new DateTimeImmutable())->modify('-' . $days . ' days');

        return new VisitStatsDTO(
            totalVisits: $this->visitRepository->countTotal($since),
            uniqueVisitors: $this->visitRepository->countUniqueVisitors($since),
            visitsByPage: $this->visitRepository->countByPage($since),
            visitsByDay: $this->visitRepository->countByDay($days),
            topReferrers: $this->visitRepository->countTopReferrers($topReferrersLimit),
        );
    }
}
