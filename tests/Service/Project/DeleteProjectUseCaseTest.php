<?php

declare(strict_types=1);

namespace App\Tests\Service\Project;

use App\Domain\Common\FileStorageInterface;
use App\Domain\Project\Exception\ProjectNotFoundException;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Service\Project\DeleteProjectUseCase;
use App\Tests\Domain\Project\ProjectMother;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteProjectUseCaseTest extends TestCase
{
    private ProjectRepositoryInterface&MockObject $projectRepository;

    private FileStorageInterface&MockObject $fileStorage;

    private DeleteProjectUseCase $useCase;

    protected function setUp(): void
    {
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->fileStorage = $this->createMock(FileStorageInterface::class);
        $this->useCase = new DeleteProjectUseCase($this->projectRepository, $this->fileStorage);
    }

    #[Test]
    public function itDeletesAProject(): void
    {
        $project = ProjectMother::create();

        $this->projectRepository->expects(self::once())
            ->method('findById')
            ->willReturn($project);

        $this->projectRepository->expects(self::once())
            ->method('remove')
            ->with($project);

        $this->useCase->execute('test-id');
    }

    #[Test]
    public function itThrowsWhenProjectNotFound(): void
    {
        $this->projectRepository->expects(self::once())
            ->method('findById')
            ->willReturn(null);

        $this->expectException(ProjectNotFoundException::class);

        $this->useCase->execute('nonexistent-id');
    }
}
