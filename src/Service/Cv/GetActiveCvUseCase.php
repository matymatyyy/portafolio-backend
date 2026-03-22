<?php

declare(strict_types=1);

namespace App\Service\Cv;

use App\Domain\Cv\CvRepositoryInterface;
use App\Service\Cv\DTO\CvDTO;

final readonly class GetActiveCvUseCase
{
    public function __construct(
        private CvRepositoryInterface $cvRepository,
    ) {
    }

    public function execute(): ?CvDTO
    {
        $cv = $this->cvRepository->findActive();

        if ($cv === null) {
            return null;
        }

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
