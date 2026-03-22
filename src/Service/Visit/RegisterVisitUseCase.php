<?php

declare(strict_types=1);

namespace App\Service\Visit;

use App\Domain\Visit\Visit;
use App\Domain\Visit\VisitId;
use App\Domain\Visit\VisitRepositoryInterface;
use App\Service\Visit\DTO\VisitDTO;

final readonly class RegisterVisitUseCase
{
    public function __construct(
        private VisitRepositoryInterface $visitRepository,
    ) {
    }

    public function execute(string $page, ?string $ipAddress, ?string $userAgent, ?string $referrer): VisitDTO
    {
        $visit = Visit::create(VisitId::generate(), $page, $ipAddress, $userAgent, $referrer);

        $this->visitRepository->save($visit);

        return new VisitDTO(
            id: $visit->id()
                ->value(),
            page: $visit->page(),
            ipAddress: $visit->ipAddress(),
            userAgent: $visit->userAgent(),
            referrer: $visit->referrer(),
            visitedAt: $visit->visitedAt(),
        );
    }
}
