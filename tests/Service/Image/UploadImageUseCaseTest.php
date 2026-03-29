<?php

declare(strict_types=1);

namespace App\Tests\Service\Image;

use App\Domain\Common\Exception\FileStorageException;
use App\Domain\Common\Exception\InvalidFileException;
use App\Domain\Common\FileStorageInterface;
use App\Service\Image\UploadImageUseCase;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UploadImageUseCaseTest extends TestCase
{
    use Assertions;

    private FileStorageInterface&MockObject $fileStorage;

    private UploadImageUseCase $useCase;

    protected function setUp(): void
    {
        $this->fileStorage = $this->createMock(FileStorageInterface::class);
        $this->useCase = new UploadImageUseCase($this->fileStorage);
    }

    #[Test]
    public function itUploadsAnImageSuccessfully(): void
    {
        $this->fileStorage->expects(self::once())
            ->method('upload')
            ->willReturnCallback(
                static fn (string $content, string $key, string $mimeType): string => 'https://pub-xxxx.r2.dev/' . $key
            );

        $result = $this->useCase->execute(
            originalFilename: 'screenshot.png',
            mimeType: 'image/png',
            fileSize: 2048,
            fileContent: 'fake-png-content',
        );

        self::shouldBeSame('screenshot.png', $result->originalFilename);
        self::shouldBeSame('image/png', $result->mimeType);
        self::shouldBeSame(2048, $result->fileSize);
        self::assertStringStartsWith('https://pub-xxxx.r2.dev/images/', $result->url);
        self::assertStringEndsWith('.png', $result->url);
    }

    #[Test]
    public function itRejectsInvalidMimeType(): void
    {
        $this->expectException(InvalidFileException::class);
        $this->expectExceptionMessageMatches('/Invalid image format/');

        $this->useCase->execute(
            originalFilename: 'document.pdf',
            mimeType: 'application/pdf',
            fileSize: 1024,
            fileContent: 'fake-content',
        );
    }

    #[Test]
    public function itRejectsOversizedFiles(): void
    {
        $this->expectException(InvalidFileException::class);
        $this->expectExceptionMessageMatches('/exceeds maximum/');

        $this->useCase->execute(
            originalFilename: 'huge-image.png',
            mimeType: 'image/png',
            fileSize: 6 * 1024 * 1024,
            fileContent: 'fake-content',
        );
    }

    #[Test]
    public function itPropagatesStorageFailures(): void
    {
        $this->fileStorage->expects(self::once())
            ->method('upload')
            ->willThrowException(FileStorageException::uploadFailed('Connection refused'));

        $this->expectException(FileStorageException::class);

        $this->useCase->execute(
            originalFilename: 'screenshot.jpg',
            mimeType: 'image/jpeg',
            fileSize: 1024,
            fileContent: 'fake-content',
        );
    }

    #[Test]
    public function itAcceptsAllAllowedMimeTypes(): void
    {
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        foreach ($allowedTypes as $mimeType => $extension) {
            $this->fileStorage->expects(self::once())
                ->method('upload')
                ->willReturnCallback(
                    static fn (string $content, string $key, string $mime): string => 'https://r2.dev/' . $key
                );

            $result = $this->useCase->execute(
                originalFilename: 'test.' . $extension,
                mimeType: $mimeType,
                fileSize: 1024,
                fileContent: 'fake-content',
            );

            self::assertStringEndsWith('.' . $extension, $result->url);

            // Reset mock for next iteration
            $this->fileStorage = $this->createMock(FileStorageInterface::class);
            $this->useCase = new UploadImageUseCase($this->fileStorage);
        }
    }
}
