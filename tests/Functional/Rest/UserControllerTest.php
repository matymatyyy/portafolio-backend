<?php

declare(strict_types=1);

namespace App\Tests\Functional\Rest;

use App\Domain\User\Email;
use App\Domain\User\HashedPassword;
use App\Domain\User\User;
use App\Domain\User\UserId;
use PDO;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private PDO $pdo;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->client->catchExceptions(false);

        /** @var PDO $pdo */
        $pdo = self::getContainer()->get(PDO::class);
        $this->pdo = $pdo;

        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        parent::tearDown();
    }

    public function testCreateUser(): void
    {
        $this->authenticatedRequest('POST', '/api/users', [
            'name' => 'John Doe',
            'email' => 'john-create@example.com',
            'password' => 'securepassword123',
        ]);

        self::assertResponseStatusCodeSame(201);

        $response = $this->getJsonResponse();
        self::assertSame('John Doe', $response['name']);
        self::assertSame('john-create@example.com', $response['email']);
        self::assertArrayHasKey('id', $response);
    }

    public function testCreateUserWithInvalidData(): void
    {
        $this->client->catchExceptions(true);

        $this->authenticatedRequest('POST', '/api/users', [
            'name' => '',
            'email' => 'invalid',
            'password' => 'short',
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testGetUser(): void
    {
        $user = $this->createAndPersistUser('Get Test', 'get-test@example.com');

        $this->authenticatedRequest('GET', sprintf('/api/users/%s', $user->id()->value()));

        self::assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        self::assertSame('Get Test', $response['name']);
    }

    public function testGetUserNotFound(): void
    {
        $this->client->catchExceptions(true);

        $nonExistentUuid = UserId::generate()->value();
        $this->authenticatedRequest('GET', sprintf('/api/users/%s', $nonExistentUuid));

        self::assertResponseStatusCodeSame(404);
    }

    public function testListUsers(): void
    {
        $this->createAndPersistUser('User 1', 'list1@example.com');
        $this->createAndPersistUser('User 2', 'list2@example.com');

        $this->authenticatedRequest('GET', '/api/users?page=1&limit=10');

        self::assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        self::assertArrayHasKey('data', $response);
        self::assertArrayHasKey('meta', $response);
        self::assertGreaterThanOrEqual(2, $response['meta']['total']);
    }

    public function testUpdateUser(): void
    {
        $user = $this->createAndPersistUser('Original', 'update-original@example.com');

        $this->authenticatedRequest('PUT', sprintf('/api/users/%s', $user->id()->value()), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        self::assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        self::assertSame('Updated Name', $response['name']);
        self::assertSame('updated@example.com', $response['email']);
    }

    public function testDeleteUser(): void
    {
        $user = $this->createAndPersistUser('To Delete', 'delete-test@example.com');

        $this->authenticatedRequest('DELETE', sprintf('/api/users/%s', $user->id()->value()));

        self::assertResponseStatusCodeSame(204);
    }

    private function createAndPersistUser(string $name, string $email): User
    {
        $user = User::create(
            UserId::generate(),
            $name,
            new Email($email),
            HashedPassword::fromHash('$2y$13$test_hash_value_for_testing'),
        );

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (id, name, email, password_hash, created_at, updated_at) VALUES (:id, :name, :email, :password_hash, :created_at, :updated_at)',
        );
        $stmt->execute([
            'id' => $user->id()
                ->value(),
            'name' => $user->name(),
            'email' => $user->email()
                ->value(),
            'password_hash' => $user->password()
                ->value(),
            'created_at' => $user->createdAt()
                ->format('Y-m-d H:i:s'),
            'updated_at' => $user->updatedAt()
                ->format('Y-m-d H:i:s'),
        ]);

        return $user;
    }

    /**
     * @param array<string, mixed>|null $body
     */
    private function authenticatedRequest(string $method, string $uri, ?array $body = null): void
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
        ];

        $content = $body !== null ? json_encode($body, JSON_THROW_ON_ERROR) : null;

        $this->client->request($method, $uri, [], [], $headers, $content);
    }

    /**
     * @return array<string, mixed>
     */
    private function getJsonResponse(): array
    {
        $content = (string) $this->client->getResponse()
            ->getContent();

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
