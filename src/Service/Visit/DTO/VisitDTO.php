<?php

declare(strict_types=1);

namespace App\Service\Visit\DTO;

use DateTimeImmutable;

final readonly class VisitDTO
{
    public function __construct(
        public string $id,
        public string $page,
        public ?string $ipAddress,
        public ?string $userAgent,
        public ?string $referrer,
        public DateTimeImmutable $visitedAt,
    ) {
    }
}
