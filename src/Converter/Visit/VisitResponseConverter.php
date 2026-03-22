<?php

declare(strict_types=1);

namespace App\Converter\Visit;

use App\Model\Response\Visit\RegisterVisitResponse;
use App\Model\Response\Visit\VisitStatsResponse;
use App\Service\Visit\DTO\VisitDTO;
use App\Service\Visit\DTO\VisitStatsDTO;

final class VisitResponseConverter
{
    public function toRegisterResponse(VisitDTO $dto): RegisterVisitResponse
    {
        return new RegisterVisitResponse(
            id: $dto->id,
            visited_at: $dto->visitedAt->format(\DateTimeInterface::ATOM),
        );
    }

    public function toStatsResponse(VisitStatsDTO $dto): VisitStatsResponse
    {
        return new VisitStatsResponse(
            total_visits: $dto->totalVisits,
            unique_visitors: $dto->uniqueVisitors,
            visits_by_page: $dto->visitsByPage,
            visits_by_day: $dto->visitsByDay,
            top_referrers: $dto->topReferrers,
        );
    }
}
