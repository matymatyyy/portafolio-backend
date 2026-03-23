<?php

declare(strict_types=1);

namespace App\Tests\Service\Cv;

use App\Domain\Common\TransactionManagerInterface;
use App\Domain\Cv\CvRepositoryInterface;
use App\Service\Cv\UploadCvUseCase;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UploadCvUseCaseTest extends TestCase
{
    use Assertions;

    private CvRepositoryInterface&MockObject $cvRepository;

    private TransactionManagerInterface&MockObject $transactionManager;

    private UploadCvUseCase $useCase;

    protected function setUp(): void
    {
        $this->cvRepository = $this->createMock(CvRepositoryInterface::class);
        $this->transactionManager = $this->createMock(TransactionManagerInterface::class);
        $this->useCase = new UploadCvUseCase($this->cvRepository, $this->transactionManager);
    }

    #[Test]
    public function itUploadsACvSuccessfully(): void
    {
        $this->transactionManager->expects(self::once())
            ->method('transactional')
            ->willReturnCallback(static fn (callable $operation) => $operation());

        $this->cvRepository->expects(self::once())
            ->method('deactivateAll');

        $this->cvRepository->expects(self::once())
            ->method('save');

        $result = $this->useCase->execute(
            originalFilename: 'resume.pdf',
            mimeType: 'application/pdf',
            fileSize: 1024,
            fileContent: 'fake-pdf-content',
        );

        self::shouldBeSame('resume.pdf', $result->originalFilename);
        self::shouldBeSame('application/pdf', $result->mimeType);
        self::shouldBeSame(1024, $result->fileSize);
        self::shouldBeTrue($result->isActive);
        self::shouldNotBeEmpty($result->id);
    }

    #[Test]
    public function itRejectsInvalidMimeType(): void
    {
        $this->expectException(\App\Domain\Cv\Exception\InvalidCvFormatException::class);

        $this->useCase->execute(
            originalFilename: 'malware.exe',
            mimeType: 'application/x-executable',
            fileSize: 1024,
            fileContent: 'fake-content',
        );
    }
}
