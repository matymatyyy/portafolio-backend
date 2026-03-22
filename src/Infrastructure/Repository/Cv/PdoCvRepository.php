<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Cv;

use App\Domain\Cv\Cv;
use App\Domain\Cv\CvId;
use App\Domain\Cv\CvRepositoryInterface;
use DateTimeImmutable;
use PDO;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(CvRepositoryInterface::class)]
final readonly class PdoCvRepository implements CvRepositoryInterface
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function save(Cv $cv): void
    {
        $sql = <<<'SQL'
            INSERT INTO curriculum_vitae (id, original_filename, mime_type, file_size, file_content, is_active, uploaded_at, updated_at)
            VALUES (:id, :original_filename, :mime_type, :file_size, :file_content, :is_active, :uploaded_at, :updated_at)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('id', $cv->id()->value());
        $stmt->bindValue('original_filename', $cv->originalFilename());
        $stmt->bindValue('mime_type', $cv->mimeType());
        $stmt->bindValue('file_size', $cv->fileSize(), PDO::PARAM_INT);
        $stmt->bindValue('file_content', $cv->fileContent(), PDO::PARAM_LOB);
        $stmt->bindValue('is_active', $cv->isActive(), PDO::PARAM_BOOL);
        $stmt->bindValue('uploaded_at', $cv->uploadedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue('updated_at', $cv->updatedAt()->format('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public function findActive(): ?Cv
    {
        $sql = <<<'SQL'
            SELECT id, original_filename, mime_type, file_size, file_content, is_active, uploaded_at, updated_at
            FROM curriculum_vitae
            WHERE is_active = TRUE
            ORDER BY uploaded_at DESC
            LIMIT 1
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        /** @var array{id: string, original_filename: string, mime_type: string, file_size: string, file_content: string, is_active: string, uploaded_at: string, updated_at: string}|false $row */
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        $fileContent = is_resource($row['file_content'])
            ? stream_get_contents($row['file_content'])
            : $row['file_content'];

        return Cv::reconstitute(
            CvId::fromString($row['id']),
            $row['original_filename'],
            $row['mime_type'],
            (int) $row['file_size'],
            (string) $fileContent,
            (bool) $row['is_active'],
            new DateTimeImmutable($row['uploaded_at']),
            new DateTimeImmutable($row['updated_at']),
        );
    }

    public function deactivateAll(): void
    {
        $this->pdo->exec(
            'UPDATE curriculum_vitae SET is_active = FALSE, updated_at = NOW() WHERE is_active = TRUE',
        );
    }
}
