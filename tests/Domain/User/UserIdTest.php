<?php

declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\Exception\InvalidArgumentException;
use App\Domain\User\UserId;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserIdTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itCreatesFromString(): void
    {
        $id = UserId::fromString('550e8400-e29b-41d4-a716-446655440000');

        self::shouldBeSame('550e8400-e29b-41d4-a716-446655440000', $id->value());
    }

    #[Test]
    public function itGeneratesUniqueIds(): void
    {
        $id1 = UserId::generate();
        $id2 = UserId::generate();

        self::shouldBeFalse($id1->equals($id2));
    }

    #[Test]
    public function itRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UserId::fromString('');
    }

    #[Test]
    public function itComparesEquality(): void
    {
        $id1 = UserId::fromString('same-id');
        $id2 = UserId::fromString('same-id');
        $id3 = UserId::fromString('different-id');

        self::shouldBeTrue($id1->equals($id2));
        self::shouldBeFalse($id1->equals($id3));
    }

    #[Test]
    public function itCanBeCastToString(): void
    {
        $id = UserId::fromString('test-id');

        self::shouldBeSame('test-id', (string) $id);
    }
}
