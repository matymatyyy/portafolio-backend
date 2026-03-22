<?php

declare(strict_types=1);

namespace App\Converter\Cv;

use App\Model\Response\Cv\CvResponse;
use App\Service\Cv\DTO\CvDTO;

final class CvResponseConverter
{
    public function toResponse(CvDTO $dto): CvResponse
    {
        return new CvResponse(
            id: $dto->id,
            original_filename: $dto->originalFilename,
            mime_type: $dto->mimeType,
            file_size: $dto->fileSize,
            is_active: $dto->isActive,
            uploaded_at: $dto->uploadedAt->format(\DateTimeInterface::ATOM),
            updated_at: $dto->updatedAt->format(\DateTimeInterface::ATOM),
        );
    }
}
