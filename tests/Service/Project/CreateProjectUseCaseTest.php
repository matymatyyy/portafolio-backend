<?php

declare(strict_types=1);

namespace App\Tests\Service\Project;

use App\Converter\Project\ProjectDomainConverter;
use App\Domain\Project\Exception\ProjectAlreadyExistsException;
use App\Domain\Project\Project;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\Slug;
use App\Service\Project\CreateProjectUseCase;
use App\Service\Project\DTO\CreateProjectDTO;
use App\Tests\Shared\Assertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateProjectUseCaseTest extends TestCase
{
    use Assertions;

    private ProjectRepositoryInterface&MockObject $projectRepository;

    private CreateProjectUseCase $useCase;

    protected function setUp(): void
    {
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->useCase = new CreateProjectUseCase($this->projectRepository, new ProjectDomainConverter());
    }

    #[Test]
    public function itCreatesAProjectSuccessfully(): void
    {
        $dto = new CreateProjectDTO(
            'My Project',
            'A description',
            'https://example.com/image.png',
            'https://example.com',
            'https://github.com/user/repo',
            ['PHP', 'Symfony'],
            'active',
        );

        $this->projectRepository->expects(self::once())
            ->method('findBySlug')
            ->willReturn(null);

        $this->projectRepository->expects(self::once())
            ->method('save');

        $result = $this->useCase->execute($dto);

        self::shouldBeSame('My Project', $result->title);
        self::shouldBeSame('my-project', $result->slug);
        self::shouldBeSame('A description', $result->description);
        self::shouldBeSame(['PHP', 'Symfony'], $result->technologies);
        self::shouldBeSame('active', $result->status);
        self::shouldNotBeEmpty($result->id);
    }

    #[Test]
    public function itThrowsWhenSlugAlreadyExists(): void
    {
        $dto = new CreateProjectDTO('My Project', 'A description', null, null, null, [], 'active');

        $existingProject = $this->createMock(Project::class);

        $this->projectRepository->expects(self::once())
            ->method('findBySlug')
            ->with(self::callback(static fn (Slug $slug) => $slug->value() === 'my-project'))
            ->willReturn($existingProject);

        $this->expectException(ProjectAlreadyExistsException::class);

        $this->useCase->execute($dto);
    }
}
