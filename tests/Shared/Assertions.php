<?php

declare(strict_types=1);

namespace App\Tests\Shared;

use PHPUnit\Framework\Assert;

trait Assertions
{
    protected static function shouldBeSame(mixed $expected, mixed $actual): void
    {
        Assert::assertSame($expected, $actual);
    }

    protected static function shouldBeNull(mixed $actual): void
    {
        Assert::assertNull($actual);
    }

    protected static function shouldNotBeNull(mixed $actual): void
    {
        Assert::assertNotNull($actual);
    }

    protected static function shouldBeTrue(bool $actual): void
    {
        Assert::assertTrue($actual);
    }

    protected static function shouldBeFalse(bool $actual): void
    {
        Assert::assertFalse($actual);
    }

    protected static function shouldNotBeEmpty(mixed $actual): void
    {
        Assert::assertNotEmpty($actual);
    }

    protected static function shouldBeGreaterThanOrEqualTo(mixed $expected, mixed $actual): void
    {
        Assert::assertGreaterThanOrEqual($expected, $actual);
    }
}
