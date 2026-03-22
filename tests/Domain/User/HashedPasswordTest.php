<?php

declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\Exception\InvalidArgumentException;
use App\Domain\User\HashedPassword;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HashedPasswordTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itCreatesFromHash(): void
    {
        $password = HashedPassword::fromHash('$2y$13$hashed_value');

        self::shouldBeSame('$2y$13$hashed_value', $password->value());
    }

    #[Test]
    public function itRejectsEmptyHash(): void
    {
        $this->expectException(InvalidArgumentException::class);

        HashedPassword::fromHash('');
    }

    #[Test]
    public function itCanBeCastToString(): void
    {
        $password = HashedPassword::fromHash('hashed');

        self::shouldBeSame('hashed', (string) $password);
    }
}
