<?php

declare(strict_types=1);

namespace App\Service\Cv;

use App\Domain\Cv\CvRepositoryInterface;
use App\Domain\Cv\Exception\CvNotFoundException;
use App\Service\Cv\DTO\CvFileDTO;

final readonly class DownloadCvUseCase
{
    public function __construct(
        private CvRepositoryInterface $cvRepository,
    ) {
    }

    public function execute(): CvFileDTO
    {
        $cv = $this->cvRepository->findActive();

        if ($cv === null) {
            throw CvNotFoundException::noActiveCv();
        }

        return new CvFileDTO(
            originalFilename: $cv->originalFilename(),
            mimeType: $cv->mimeType(),
            fileSize: $cv->fileSize(),
            fileContent: $cv->fileContent(),
        );
    }
}
