<?php

declare(strict_types=1);

namespace App\Tests\Service\Project;

use App\Converter\Project\ProjectDomainConverter;
use App\Domain\Project\Exception\ProjectNotFoundException;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Service\Project\GetProjectUseCase;
use App\Tests\Domain\Project\ProjectMother;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetProjectUseCaseTest extends TestCase
{
    use Assertions;

    private ProjectRepositoryInterface&MockObject $projectRepository;

    private GetProjectUseCase $useCase;

    protected function setUp(): void
    {
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->useCase = new GetProjectUseCase($this->projectRepository, new ProjectDomainConverter());
    }

    #[Test]
    public function itReturnsAProjectById(): void
    {
        $project = ProjectMother::create();

        $this->projectRepository->expects(self::once())
            ->method('findById')
            ->willReturn($project);

        $result = $this->useCase->execute('test-id');

        self::shouldBeSame('test-id', $result->id);
        self::shouldBeSame('My Project', $result->title);
        self::shouldBeSame('my-project', $result->slug);
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
