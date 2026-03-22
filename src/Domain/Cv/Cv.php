<?php

declare(strict_types=1);

namespace App\Domain\Cv;

use App\Domain\Cv\Exception\InvalidCvFormatException;
use DateTimeImmutable;

class Cv
{
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    private CvId $id;

    private string $originalFilename;

    private string $mimeType;

    private int $fileSize;

    private string $fileContent;

    private bool $isActive;

    private DateTimeImmutable $uploadedAt;

    private DateTimeImmutable $updatedAt;

    public function __construct(
        CvId $id,
        string $originalFilename,
        string $mimeType,
        int $fileSize,
        string $fileContent,
        bool $isActive,
        DateTimeImmutable $uploadedAt,
        DateTimeImmutable $updatedAt,
    ) {
        self::validateMimeType($mimeType);

        $this->id = $id;
        $this->originalFilename = $originalFilename;
        $this->mimeType = $mimeType;
        $this->fileSize = $fileSize;
        $this->fileContent = $fileContent;
        $this->isActive = $isActive;
        $this->uploadedAt = $uploadedAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        CvId $id,
        string $originalFilename,
        string $mimeType,
        int $fileSize,
        string $fileContent,
    ): self {
        $now = new DateTimeImmutable();

        return new self($id, $originalFilename, $mimeType, $fileSize, $fileContent, true, $now, $now);
    }

    public static function reconstitute(
        CvId $id,
        string $originalFilename,
        string $mimeType,
        int $fileSize,
        string $fileContent,
        bool $isActive,
        DateTimeImmutable $uploadedAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $originalFilename, $mimeType, $fileSize, $fileContent, $isActive, $uploadedAt, $updatedAt);
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function id(): CvId
    {
        return $this->id;
    }

    public function originalFilename(): string
    {
        return $this->originalFilename;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function fileSize(): int
    {
        return $this->fileSize;
    }

    public function fileContent(): string
    {
        return $this->fileContent;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function uploadedAt(): DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private static function validateMimeType(string $mimeType): void
    {
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw InvalidCvFormatException::forMimeType($mimeType);
        }
    }
}
