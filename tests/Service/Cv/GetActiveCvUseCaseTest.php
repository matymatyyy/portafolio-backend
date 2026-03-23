<?php

declare(strict_types=1);

namespace App\Tests\Service\Cv;

use App\Domain\Cv\Cv;
use App\Domain\Cv\CvId;
use App\Domain\Cv\CvRepositoryInterface;
use App\Service\Cv\GetActiveCvUseCase;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetActiveCvUseCaseTest extends TestCase
{
    use Assertions;

    private CvRepositoryInterface&MockObject $cvRepository;

    private GetActiveCvUseCase $useCase;

    protected function setUp(): void
    {
        $this->cvRepository = $this->createMock(CvRepositoryInterface::class);
        $this->useCase = new GetActiveCvUseCase($this->cvRepository);
    }

    #[Test]
    public function itReturnsActiveCvDto(): void
    {
        $cv = Cv::create(
            CvId::generate(),
            'resume.pdf',
            'application/pdf',
            2048,
            'pdf-content',
        );

        $this->cvRepository->expects(self::once())
            ->method('findActive')
            ->willReturn($cv);

        $result = $this->useCase->execute();

        self::shouldNotBeNull($result);
        self::shouldBeSame('resume.pdf', $result->originalFilename);
        self::shouldBeSame('application/pdf', $result->mimeType);
        self::shouldBeTrue($result->isActive);
    }

    #[Test]
    public function itReturnsNullWhenNoActiveCv(): void
    {
        $this->cvRepository->expects(self::once())
            ->method('findActive')
            ->willReturn(null);

        $result = $this->useCase->execute();

        self::shouldBeNull($result);
    }
}
