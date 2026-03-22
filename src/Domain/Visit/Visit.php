<?php

declare(strict_types=1);

namespace App\Domain\Visit;

use DateTimeImmutable;

class Visit
{
    private VisitId $id;

    private string $page;

    private ?string $ipAddress;

    private ?string $userAgent;

    private ?string $referrer;

    private DateTimeImmutable $visitedAt;

    public function __construct(
        VisitId $id,
        string $page,
        ?string $ipAddress,
        ?string $userAgent,
        ?string $referrer,
        DateTimeImmutable $visitedAt,
    ) {
        $this->id = $id;
        $this->page = $page;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->referrer = $referrer;
        $this->visitedAt = $visitedAt;
    }

    public static function create(
        VisitId $id,
        string $page,
        ?string $ipAddress,
        ?string $userAgent,
        ?string $referrer,
    ): self {
        return new self($id, $page, $ipAddress, $userAgent, $referrer, new DateTimeImmutable());
    }

    public static function reconstitute(
        VisitId $id,
        string $page,
        ?string $ipAddress,
        ?string $userAgent,
        ?string $referrer,
        DateTimeImmutable $visitedAt,
    ): self {
        return new self($id, $page, $ipAddress, $userAgent, $referrer, $visitedAt);
    }

    public function id(): VisitId
    {
        return $this->id;
    }

    public function page(): string
    {
        return $this->page;
    }

    public function ipAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function userAgent(): ?string
    {
        return $this->userAgent;
    }

    public function referrer(): ?string
    {
        return $this->referrer;
    }

    public function visitedAt(): DateTimeImmutable
    {
        return $this->visitedAt;
    }
}
