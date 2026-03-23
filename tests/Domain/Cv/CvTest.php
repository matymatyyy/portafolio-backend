<?php

declare(strict_types=1);

namespace App\Tests\Domain\Cv;

use App\Domain\Cv\Cv;
use App\Domain\Cv\CvId;
use App\Domain\Cv\Exception\InvalidCvFormatException;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CvTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itCreatesACvWithValidPdf(): void
    {
        $cv = Cv::create(CvId::generate(), 'resume.pdf', 'application/pdf', 1024, 'content');

        self::shouldBeSame('resume.pdf', $cv->originalFilename());
        self::shouldBeSame('application/pdf', $cv->mimeType());
        self::shouldBeSame(1024, $cv->fileSize());
        self::shouldBeTrue($cv->isActive());
    }

    #[Test]
    public function itCreatesACvWithDocx(): void
    {
        $cv = Cv::create(
            CvId::generate(),
            'resume.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            2048,
            'content',
        );

        self::shouldBeSame('resume.docx', $cv->originalFilename());
    }

    #[Test]
    public function itRejectsInvalidMimeType(): void
    {
        $this->expectException(InvalidCvFormatException::class);

        Cv::create(CvId::generate(), 'photo.png', 'image/png', 1024, 'content');
    }

    #[Test]
    public function itDeactivatesACv(): void
    {
        $cv = Cv::create(CvId::generate(), 'resume.pdf', 'application/pdf', 1024, 'content');

        self::shouldBeTrue($cv->isActive());

        $cv->deactivate();

        self::shouldBeFalse($cv->isActive());
    }
}
