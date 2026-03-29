<?php

declare(strict_types=1);

namespace App\Converter\Image;

use App\Model\Response\Image\ImageResponse;
use App\Service\Image\DTO\ImageDTO;

final class ImageResponseConverter
{
    public function toResponse(ImageDTO $dto): ImageResponse
    {
        return new ImageResponse(
            url: $dto->url,
            original_filename: $dto->originalFilename,
            mime_type: $dto->mimeType,
            file_size: $dto->fileSize,
        );
    }
}
