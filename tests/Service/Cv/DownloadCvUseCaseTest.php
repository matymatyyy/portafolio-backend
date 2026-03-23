<?php

declare(strict_types=1);

namespace App\Tests\Service\Cv;

use App\Domain\Cv\Cv;
use App\Domain\Cv\CvId;
use App\Domain\Cv\CvRepositoryInterface;
use App\Domain\Cv\Exception\CvNotFoundException;
use App\Service\Cv\DownloadCvUseCase;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DownloadCvUseCaseTest extends TestCase
{
    use Assertions;

    private CvRepositoryInterface&MockObject $cvRepository;

    private DownloadCvUseCase $useCase;

    protected function setUp(): void
    {
        $this->cvRepository = $this->createMock(CvRepositoryInterface::class);
        $this->useCase = new DownloadCvUseCase($this->cvRepository);
    }

    #[Test]
    public function itDownloadsTheActiveCv(): void
    {
        $cv = Cv::create(
            CvId::generate(),
            'resume.pdf',
            'application/pdf',
            2048,
            'pdf-binary-content',
        );

        $this->cvRepository->expects(self::once())
            ->method('findActive')
            ->willReturn($cv);

        $result = $this->useCase->execute();

        self::shouldBeSame('resume.pdf', $result->originalFilename);
        self::shouldBeSame('application/pdf', $result->mimeType);
        self::shouldBeSame(2048, $result->fileSize);
        self::shouldBeSame('pdf-binary-content', $result->fileContent);
    }

    #[Test]
    public function itThrowsWhenNoActiveCv(): void
    {
        $this->cvRepository->expects(self::once())
            ->method('findActive')
            ->willReturn(null);

        $this->expectException(CvNotFoundException::class);

        $this->useCase->execute();
    }
}
