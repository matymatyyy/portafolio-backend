<?php

declare(strict_types=1);

namespace App\Tests\Service\Visit;

use App\Domain\Visit\VisitRepositoryInterface;
use App\Service\Visit\RegisterVisitUseCase;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RegisterVisitUseCaseTest extends TestCase
{
    use Assertions;

    private VisitRepositoryInterface&MockObject $visitRepository;

    private RegisterVisitUseCase $useCase;

    protected function setUp(): void
    {
        $this->visitRepository = $this->createMock(VisitRepositoryInterface::class);
        $this->useCase = new RegisterVisitUseCase($this->visitRepository);
    }

    #[Test]
    public function itRegistersAVisit(): void
    {
        $this->visitRepository->expects(self::once())
            ->method('save');

        $result = $this->useCase->execute(
            page: '/projects',
            ipAddress: '192.168.1.1',
            userAgent: 'Mozilla/5.0',
            referrer: 'https://google.com',
        );

        self::shouldBeSame('/projects', $result->page);
        self::shouldBeSame('192.168.1.1', $result->ipAddress);
        self::shouldBeSame('Mozilla/5.0', $result->userAgent);
        self::shouldBeSame('https://google.com', $result->referrer);
        self::shouldNotBeEmpty($result->id);
    }

    #[Test]
    public function itRegistersAVisitWithNullableFields(): void
    {
        $this->visitRepository->expects(self::once())
            ->method('save');

        $result = $this->useCase->execute(page: '/', ipAddress: null, userAgent: null, referrer: null);

        self::shouldBeSame('/', $result->page);
        self::shouldBeNull($result->ipAddress);
        self::shouldBeNull($result->userAgent);
        self::shouldBeNull($result->referrer);
    }
}
