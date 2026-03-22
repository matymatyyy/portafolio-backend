<?php

declare(strict_types=1);

namespace App\Tests\Domain\Project;

use App\Domain\Project\Exception\InvalidArgumentException;
use App\Domain\Project\Slug;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SlugTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itCreatesFromTitle(): void
    {
        $slug = Slug::fromTitle('My Portfolio Website');

        self::shouldBeSame('my-portfolio-website', $slug->value());
    }

    #[Test]
    public function itHandlesSpecialCharacters(): void
    {
        $slug = Slug::fromTitle('Hello World! @#$%');

        self::shouldBeSame('hello-world', $slug->value());
    }

    #[Test]
    public function itHandlesMultipleSpacesAndDashes(): void
    {
        $slug = Slug::fromTitle('My   Project---Name');

        self::shouldBeSame('my-project-name', $slug->value());
    }

    #[Test]
    public function itCreatesFromString(): void
    {
        $slug = Slug::fromString('existing-slug');

        self::shouldBeSame('existing-slug', $slug->value());
    }

    #[Test]
    public function itRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Slug('');
    }

    #[Test]
    public function itComparesEquality(): void
    {
        $slug1 = Slug::fromString('same-slug');
        $slug2 = Slug::fromString('same-slug');
        $slug3 = Slug::fromString('different-slug');

        self::shouldBeTrue($slug1->equals($slug2));
        self::shouldBeFalse($slug1->equals($slug3));
    }

    #[Test]
    public function itCanBeCastToString(): void
    {
        $slug = Slug::fromString('test-slug');

        self::shouldBeSame('test-slug', (string) $slug);
    }
}
