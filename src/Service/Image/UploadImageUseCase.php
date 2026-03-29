<?php

declare(strict_types=1);

namespace App\Service\Image;

use App\Domain\Common\Exception\InvalidFileException;
use App\Domain\Common\FileStorageInterface;
use App\Service\Image\DTO\ImageDTO;
use Symfony\Component\Uid\Uuid;

final readonly class UploadImageUseCase
{
    private const array ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    private const int MAX_FILE_SIZE = 5 * 1024 * 1024;

    private const array MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    public function __construct(
        private FileStorageInterface $fileStorage,
    ) {
    }

    public function execute(
        string $originalFilename,
        string $mimeType,
        int $fileSize,
        string $fileContent,
    ): ImageDTO {
        $this->validateMimeType($mimeType);
        $this->validateFileSize($fileSize);

        $extension = self::MIME_EXTENSIONS[$mimeType];
        $key = 'images/' . Uuid::v4()->toRfc4122() . '.' . $extension;

        $url = $this->fileStorage->upload($fileContent, $key, $mimeType);

        return new ImageDTO(
            url: $url,
            originalFilename: $originalFilename,
            mimeType: $mimeType,
            fileSize: $fileSize,
        );
    }

    private function validateMimeType(string $mimeType): void
    {
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw InvalidFileException::forMimeType($mimeType);
        }
    }

    private function validateFileSize(int $fileSize): void
    {
        if ($fileSize > self::MAX_FILE_SIZE) {
            throw InvalidFileException::forFileSize($fileSize, self::MAX_FILE_SIZE);
        }
    }
}
