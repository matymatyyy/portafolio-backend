<?php

declare(strict_types=1);

namespace App\Tests\Domain\Project;

use App\Domain\Project\Slug;
use App\Domain\Project\Status;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    use Assertions;

    #[Test]
    public function itCreatesAProject(): void
    {
        $project = ProjectMother::create(
            imageUrl: 'https://example.com/image.png',
            projectUrl: 'https://example.com',
            repoUrl: 'https://github.com/user/repo',
            technologies: ['PHP', 'Symfony'],
        );

        self::shouldBeSame('test-id', $project->id()->value());
        self::shouldBeSame('My Project', $project->title());
        self::shouldBeSame('my-project', $project->slug()->value());
        self::shouldBeSame('A description', $project->description());
        self::shouldBeSame('https://example.com/image.png', $project->imageUrl());
        self::shouldBeSame('https://example.com', $project->projectUrl());
        self::shouldBeSame('https://github.com/user/repo', $project->repoUrl());
        self::shouldBeSame(['PHP', 'Symfony'], $project->technologies());
        self::shouldBeSame(Status::Active, $project->status());
        self::shouldNotBeNull($project->createdAt());
        self::shouldNotBeNull($project->updatedAt());
    }

    #[Test]
    public function itUpdatesAProject(): void
    {
        $project = ProjectMother::create(
            imageUrl: 'https://example.com/image.png',
            projectUrl: 'https://example.com',
            repoUrl: 'https://github.com/user/repo',
            technologies: ['PHP', 'Symfony'],
        );
        $originalUpdatedAt = $project->updatedAt();

        $project->update(
            'Updated Title',
            Slug::fromTitle('Updated Title'),
            'Updated description',
            null,
            'https://new-url.com',
            null,
            ['TypeScript', 'React'],
            Status::Archived,
            1,
        );

        self::shouldBeSame('Updated Title', $project->title());
        self::shouldBeSame('updated-title', $project->slug()->value());
        self::shouldBeSame('Updated description', $project->description());
        self::shouldBeNull($project->imageUrl());
        self::shouldBeSame('https://new-url.com', $project->projectUrl());
        self::shouldBeNull($project->repoUrl());
        self::shouldBeSame(['TypeScript', 'React'], $project->technologies());
        self::shouldBeSame(Status::Archived, $project->status());
        self::shouldBeGreaterThanOrEqualTo($originalUpdatedAt, $project->updatedAt());
    }

    #[Test]
    public function itCreatesWithNullableFields(): void
    {
        $project = ProjectMother::create(
            title: 'Minimal Project',
            description: 'Description only',
            technologies: [],
        );

        self::shouldBeNull($project->imageUrl());
        self::shouldBeNull($project->projectUrl());
        self::shouldBeNull($project->repoUrl());
        self::shouldBeSame([], $project->technologies());
    }
}
