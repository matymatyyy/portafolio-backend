<?php

declare(strict_types=1);

namespace App\Tests\Domain\Project;

use App\Domain\Project\Exception\InvalidArgumentException;
use App\Domain\Project\ProjectId;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProjectIdTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itCreatesFromString(): void
    {
        $id = ProjectId::fromString('550e8400-e29b-41d4-a716-446655440000');

        self::shouldBeSame('550e8400-e29b-41d4-a716-446655440000', $id->value());
    }

    #[Test]
    public function itGeneratesUniqueIds(): void
    {
        $id1 = ProjectId::generate();
        $id2 = ProjectId::generate();

        self::shouldBeFalse($id1->equals($id2));
    }

    #[Test]
    public function itRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ProjectId::fromString('');
    }

    #[Test]
    public function itComparesEquality(): void
    {
        $id1 = ProjectId::fromString('same-id');
        $id2 = ProjectId::fromString('same-id');
        $id3 = ProjectId::fromString('different-id');

        self::shouldBeTrue($id1->equals($id2));
        self::shouldBeFalse($id1->equals($id3));
    }

    #[Test]
    public function itCanBeCastToString(): void
    {
        $id = ProjectId::fromString('test-id');

        self::shouldBeSame('test-id', (string) $id);
    }
}
