<?php

declare(strict_types=1);

namespace App\Tests\Service\Visit;

use App\Domain\Visit\VisitRepositoryInterface;
use App\Service\Visit\GetVisitStatsUseCase;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetVisitStatsUseCaseTest extends TestCase
{
    use Assertions;

    private VisitRepositoryInterface&MockObject $visitRepository;

    private GetVisitStatsUseCase $useCase;

    protected function setUp(): void
    {
        $this->visitRepository = $this->createMock(VisitRepositoryInterface::class);
        $this->useCase = new GetVisitStatsUseCase($this->visitRepository);
    }

    #[Test]
    public function itReturnsVisitStats(): void
    {
        $this->visitRepository->expects(self::once())
            ->method('countTotal')
            ->willReturn(100);

        $this->visitRepository->expects(self::once())
            ->method('countUniqueVisitors')
            ->willReturn(50);

        $this->visitRepository->expects(self::once())
            ->method('countByPage')
            ->willReturn(['/' => 60, '/projects' => 40]);

        $this->visitRepository->expects(self::once())
            ->method('countByDay')
            ->willReturn([['date' => '2026-03-20', 'count' => 10]]);

        $this->visitRepository->expects(self::once())
            ->method('countTopReferrers')
            ->willReturn([['referrer' => 'https://google.com', 'count' => 25]]);

        $result = $this->useCase->execute(30, 10);

        self::shouldBeSame(100, $result->totalVisits);
        self::shouldBeSame(50, $result->uniqueVisitors);
        self::shouldBeSame(60, $result->visitsByPage['/']);
        self::shouldBeSame(40, $result->visitsByPage['/projects']);
    }

    #[Test]
    public function itClampsDaysToValidRange(): void
    {
        $this->visitRepository->method('countTotal')->willReturn(0);
        $this->visitRepository->method('countUniqueVisitors')->willReturn(0);
        $this->visitRepository->method('countByPage')->willReturn([]);
        $this->visitRepository->method('countByDay')->willReturn([]);
        $this->visitRepository->method('countTopReferrers')->willReturn([]);

        $result = $this->useCase->execute(0, 10);

        self::shouldBeSame(0, $result->totalVisits);
    }
}
