<?php

declare(strict_types=1);

namespace App\Tests\Domain\Visit;

use App\Domain\Visit\Visit;
use App\Domain\Visit\VisitId;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class VisitTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itCreatesAVisit(): void
    {
        $visit = Visit::create(
            VisitId::generate(),
            '/projects',
            '192.168.1.1',
            'Mozilla/5.0',
            'https://google.com',
        );

        self::shouldBeSame('/projects', $visit->page());
        self::shouldBeSame('192.168.1.1', $visit->ipAddress());
        self::shouldBeSame('Mozilla/5.0', $visit->userAgent());
        self::shouldBeSame('https://google.com', $visit->referrer());
        self::shouldNotBeEmpty($visit->id()->value());
    }

    #[Test]
    public function itCreatesAVisitWithNullableFields(): void
    {
        $visit = Visit::create(VisitId::generate(), '/', null, null, null);

        self::shouldBeSame('/', $visit->page());
        self::shouldBeNull($visit->ipAddress());
        self::shouldBeNull($visit->userAgent());
        self::shouldBeNull($visit->referrer());
    }

    #[Test]
    public function itReconstitutesAVisit(): void
    {
        $id = VisitId::generate();
        $visitedAt = new \DateTimeImmutable('2026-01-15 10:00:00');

        $visit = Visit::reconstitute($id, '/about', '10.0.0.1', 'Chrome', 'https://example.com', $visitedAt);

        self::shouldBeSame($id->value(), $visit->id()->value());
        self::shouldBeSame('/about', $visit->page());
        self::shouldBeSame('2026-01-15 10:00:00', $visit->visitedAt()->format('Y-m-d H:i:s'));
    }
}
