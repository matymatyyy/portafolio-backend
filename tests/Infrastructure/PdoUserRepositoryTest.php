<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure;

use App\Domain\User\Email;
use App\Domain\User\HashedPassword;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use PDO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PdoUserRepositoryTest extends KernelTestCase
{
    private UserRepositoryInterface $repository;

    private PDO $pdo;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var PDO $pdo */
        $pdo = self::getContainer()->get(PDO::class);
        $this->pdo = $pdo;

        /** @var UserRepositoryInterface $repository */
        $repository = self::getContainer()->get(UserRepositoryInterface::class);
        $this->repository = $repository;

        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        parent::tearDown();
    }

    public function testSaveAndFindById(): void
    {
        $user = User::create(
            UserId::fromString('integration-test-id'),
            'Integration Test',
            new Email('integration@example.com'),
            HashedPassword::fromHash('$2y$13$test_hash'),
        );

        $this->repository->save($user);

        $found = $this->repository->findById(UserId::fromString('integration-test-id'));

        self::assertNotNull($found);
        self::assertSame('Integration Test', $found->name());
        self::assertSame('integration@example.com', $found->email()->value());
    }

    public function testFindByEmail(): void
    {
        $user = User::create(
            UserId::fromString('email-test-id'),
            'Email Test',
            new Email('email-test@example.com'),
            HashedPassword::fromHash('$2y$13$test_hash'),
        );

        $this->repository->save($user);

        $found = $this->repository->findByEmail(new Email('email-test@example.com'));

        self::assertNotNull($found);
        self::assertSame('email-test-id', $found->id()->value());
    }

    public function testFindByEmailReturnsNullWhenNotFound(): void
    {
        $found = $this->repository->findByEmail(new Email('nonexistent@example.com'));

        self::assertNull($found);
    }

    public function testFindPaginated(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $user = User::create(
                UserId::generate(),
                sprintf('User %d', $i),
                new Email(sprintf('paginated-%d@example.com', $i)),
                HashedPassword::fromHash('$2y$13$test_hash'),
            );
            $this->repository->save($user);
        }

        $result = $this->repository->findPaginated(1, 10);

        self::assertCount(10, $result['items']);
        self::assertSame(15, $result['total']);
    }

    public function testFindPaginatedWithFilters(): void
    {
        $user = User::create(
            UserId::generate(),
            'Filtered User',
            new Email('filtered-unique@example.com'),
            HashedPassword::fromHash('$2y$13$test_hash'),
        );
        $this->repository->save($user);

        $result = $this->repository->findPaginated(1, 10, [
            'email' => 'filtered-unique',
        ]);

        self::assertGreaterThanOrEqual(1, $result['total']);
    }

    public function testRemove(): void
    {
        $userId = UserId::fromString('remove-test-id');
        $user = User::create(
            $userId,
            'To Remove',
            new Email('remove@example.com'),
            HashedPassword::fromHash('$2y$13$test_hash'),
        );

        $this->repository->save($user);
        $this->repository->remove($user);

        $found = $this->repository->findById($userId);
        self::assertNull($found);
    }
}
