<?php

declare(strict_types=1);

namespace App\Service\Cv;

use App\Domain\Cv\Cv;
use App\Domain\Cv\CvId;
use App\Domain\Cv\CvRepositoryInterface;
use App\Domain\Cv\Exception\CvNotFoundException;
use App\Service\Cv\DTO\CvDTO;

final readonly class UpdateCvUseCase
{
    public function __construct(
        private CvRepositoryInterface $cvRepository,
    ) {
    }

    public function execute(
        string $originalFilename,
        string $mimeType,
        int $fileSize,
        string $fileContent,
    ): CvDTO {
        $existingCv = $this->cvRepository->findActive();

        if ($existingCv === null) {
            throw CvNotFoundException::noActiveCv();
        }

        $this->cvRepository->deactivateAll();

        $cv = Cv::create(CvId::generate(), $originalFilename, $mimeType, $fileSize, $fileContent);

        $this->cvRepository->save($cv);

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
