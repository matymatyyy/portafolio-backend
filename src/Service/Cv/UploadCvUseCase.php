<?php

declare(strict_types=1);

namespace App\Service\Cv;

use App\Domain\Common\TransactionManagerInterface;
use App\Domain\Cv\Cv;
use App\Domain\Cv\CvId;
use App\Domain\Cv\CvRepositoryInterface;
use App\Service\Cv\DTO\CvDTO;

final readonly class UploadCvUseCase
{
    public function __construct(
        private CvRepositoryInterface $cvRepository,
        private TransactionManagerInterface $transactionManager,
    ) {
    }

    public function execute(
        string $originalFilename,
        string $mimeType,
        int $fileSize,
        string $fileContent,
    ): CvDTO {
        $cv = Cv::create(CvId::generate(), $originalFilename, $mimeType, $fileSize, $fileContent);

        $this->transactionManager->transactional(function () use ($cv): void {
            $this->cvRepository->deactivateAll();
            $this->cvRepository->save($cv);
        });

        return new CvDTO(
            id: $cv->id()
                ->value(),
            originalFilename: $cv->originalFilename(),
            mimeType: $cv->mimeType(),
            fileSize: $cv->fileSize(),
            isActive: $cv->isActive(),
            uploadedAt: $cv->uploadedAt(),
            updatedAt: $cv->updatedAt(),
        );
    }
}
