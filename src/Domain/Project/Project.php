<?php

declare(strict_types=1);

namespace App\Domain\Project;

use DateTimeImmutable;

class Project
{
    private ProjectId $id;

    private string $title;

    private Slug $slug;

    private string $description;

    private ?string $imageUrl;

    private ?string $projectUrl;

    private ?string $repoUrl;

    /**
     * @var string[]
     */
    private array $technologies;

    private Status $status;

    private int $sortOrder;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    /**
     * @param string[] $technologies
     */
    public function __construct(
        ProjectId $id,
        string $title,
        Slug $slug,
        string $description,
        ?string $imageUrl,
        ?string $projectUrl,
        ?string $repoUrl,
        array $technologies,
        Status $status,
        int $sortOrder,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->description = $description;
        $this->imageUrl = $imageUrl;
        $this->projectUrl = $projectUrl;
        $this->repoUrl = $repoUrl;
        $this->technologies = $technologies;
        $this->status = $status;
        $this->sortOrder = $sortOrder;
        $this->createdAt = $createdAt;
        $this->updatedAt = $createdAt;
    }

    /**
     * @param string[] $technologies
     */
    public static function create(
        ProjectId $id,
        string $title,
        Slug $slug,
        string $description,
        ?string $imageUrl,
        ?string $projectUrl,
        ?string $repoUrl,
        array $technologies,
        Status $status,
        int $sortOrder = 0,
    ): self {
        return new self(
            $id,
            $title,
            $slug,
            $description,
            $imageUrl,
            $projectUrl,
            $repoUrl,
            $technologies,
            $status,
            $sortOrder,
            new DateTimeImmutable()
        );
    }

    /**
     * @param string[] $technologies
     */
    public static function reconstitute(
        ProjectId $id,
        string $title,
        Slug $slug,
        string $description,
        ?string $imageUrl,
        ?string $projectUrl,
        ?string $repoUrl,
        array $technologies,
        Status $status,
        int $sortOrder,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        $project = new self(
            $id,
            $title,
            $slug,
            $description,
            $imageUrl,
            $projectUrl,
            $repoUrl,
            $technologies,
            $status,
            $sortOrder,
            $createdAt
        );
        $project->updatedAt = $updatedAt;

        return $project;
    }

    /**
     * @param string[] $technologies
     */
    public function update(
        string $title,
        Slug $slug,
        string $description,
        ?string $imageUrl,
        ?string $projectUrl,
        ?string $repoUrl,
        array $technologies,
        Status $status,
        int $sortOrder,
    ): void {
        $this->title = $title;
        $this->slug = $slug;
        $this->description = $description;
        $this->imageUrl = $imageUrl;
        $this->projectUrl = $projectUrl;
        $this->repoUrl = $repoUrl;
        $this->technologies = $technologies;
        $this->status = $status;
        $this->sortOrder = $sortOrder;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function id(): ProjectId
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function slug(): Slug
    {
        return $this->slug;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function imageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function projectUrl(): ?string
    {
        return $this->projectUrl;
    }

    public function repoUrl(): ?string
    {
        return $this->repoUrl;
    }

    /**
     * @return string[]
     */
    public function technologies(): array
    {
        return $this->technologies;
    }

    public function status(): Status
    {
        return $this->status;
    }

    public function sortOrder(): int
    {
        return $this->sortOrder;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
