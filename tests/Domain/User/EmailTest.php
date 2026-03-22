<?php

declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\Email;
use App\Domain\User\Exception\InvalidEmailException;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itCreatesAValidEmail(): void
    {
        $email = new Email('john@example.com');

        self::shouldBeSame('john@example.com', $email->value());
    }

    #[Test]
    public function itNormalizesEmailToLowercase(): void
    {
        $email = new Email('John@Example.COM');

        self::shouldBeSame('john@example.com', $email->value());
    }

    #[Test]
    public function itTrimsWhitespace(): void
    {
        $email = new Email('  john@example.com  ');

        self::shouldBeSame('john@example.com', $email->value());
    }

    #[Test]
    #[DataProvider('invalidEmailProvider')]
    public function itRejectsInvalidEmails(string $invalidEmail): void
    {
        $this->expectException(InvalidEmailException::class);

        new Email($invalidEmail);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidEmailProvider(): iterable
    {
        yield 'empty string' => [''];
        yield 'no at sign' => ['john.example.com'];
        yield 'no domain' => ['john@'];
        yield 'no local part' => ['@example.com'];
        yield 'spaces in middle' => ['john @example.com'];
    }

    #[Test]
    public function itComparesEquality(): void
    {
        $email1 = new Email('john@example.com');
        $email2 = new Email('john@example.com');
        $email3 = new Email('jane@example.com');

        self::shouldBeTrue($email1->equals($email2));
        self::shouldBeFalse($email1->equals($email3));
    }

    #[Test]
    public function itCanBeCastToString(): void
    {
        $email = new Email('john@example.com');

        self::shouldBeSame('john@example.com', (string) $email);
    }
}
