<?php

declare(strict_types=1);

namespace App\Tests\Service\Project;

use App\Converter\Project\ProjectDomainConverter;
use App\Domain\Project\Exception\ProjectAlreadyExistsException;
use App\Domain\Project\Exception\ProjectNotFoundException;
use App\Domain\Project\Project;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Service\Project\DTO\UpdateProjectDTO;
use App\Service\Project\UpdateProjectUseCase;
use App\Tests\Domain\Project\ProjectMother;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateProjectUseCaseTest extends TestCase
{
    use Assertions;

    private ProjectRepositoryInterface&MockObject $projectRepository;

    private UpdateProjectUseCase $useCase;

    protected function setUp(): void
    {
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->useCase = new UpdateProjectUseCase($this->projectRepository, new ProjectDomainConverter());
    }

    #[Test]
    public function itUpdatesAProjectSuccessfully(): void
    {
        $project = ProjectMother::create();

        $dto = new UpdateProjectDTO(
            'test-id',
            'Updated Project',
            'Updated description',
            'https://example.com/image.png',
            'https://example.com',
            'https://github.com/user/repo',
            ['PHP', 'Symfony'],
            'archived',
        );

        $this->projectRepository->expects(self::once())
            ->method('findById')
            ->willReturn($project);

        $this->projectRepository->expects(self::once())
            ->method('findBySlug')
            ->willReturn(null);

        $this->projectRepository->expects(self::once())
            ->method('save');

        $result = $this->useCase->execute($dto);

        self::shouldBeSame('Updated Project', $result->title);
        self::shouldBeSame('updated-project', $result->slug);
        self::shouldBeSame('archived', $result->status);
    }

    #[Test]
    public function itThrowsWhenProjectNotFound(): void
    {
        $dto = new UpdateProjectDTO('nonexistent-id', 'Title', 'Description', null, null, null, [], 'active');

        $this->projectRepository->expects(self::once())
            ->method('findById')
            ->willReturn(null);

        $this->expectException(ProjectNotFoundException::class);

        $this->useCase->execute($dto);
    }

    #[Test]
    public function itThrowsWhenNewSlugAlreadyExists(): void
    {
        $project = ProjectMother::create(technologies: []);

        $dto = new UpdateProjectDTO('test-id', 'Other Project', 'Description', null, null, null, [], 'active');

        $existingProject = $this->createMock(Project::class);

        $this->projectRepository->expects(self::once())
            ->method('findById')
            ->willReturn($project);

        $this->projectRepository->expects(self::once())
            ->method('findBySlug')
            ->willReturn($existingProject);

        $this->expectException(ProjectAlreadyExistsException::class);

        $this->useCase->execute($dto);
    }
}
