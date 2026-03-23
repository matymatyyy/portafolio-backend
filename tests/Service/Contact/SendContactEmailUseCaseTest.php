<?php

declare(strict_types=1);

namespace App\Tests\Service\Contact;

use App\Domain\Contact\Exception\ContactEmailFailedException;
use App\Domain\Contact\Service\ContactEmailServiceInterface;
use App\Service\Contact\DTO\SendContactEmailDTO;
use App\Service\Contact\SendContactEmailUseCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SendContactEmailUseCaseTest extends TestCase
{
    private ContactEmailServiceInterface&MockObject $contactEmailService;

    private SendContactEmailUseCase $useCase;

    protected function setUp(): void
    {
        $this->contactEmailService = $this->createMock(ContactEmailServiceInterface::class);
        $this->useCase = new SendContactEmailUseCase($this->contactEmailService);
    }

    #[Test]
    public function itSendsAContactEmail(): void
    {
        $dto = new SendContactEmailDTO(
            name: 'John Doe',
            email: 'john@example.com',
            subject: 'Hello',
            message: 'This is a test message.',
        );

        $this->contactEmailService->expects(self::once())
            ->method('sendContactEmail')
            ->with('John Doe', 'john@example.com', 'Hello', 'This is a test message.');

        $this->useCase->execute($dto);
    }

    #[Test]
    public function itThrowsWhenEmailServiceFails(): void
    {
        $dto = new SendContactEmailDTO(
            name: 'John Doe',
            email: 'john@example.com',
            subject: 'Hello',
            message: 'This is a test message.',
        );

        $this->contactEmailService->expects(self::once())
            ->method('sendContactEmail')
            ->willThrowException(new \RuntimeException('SMTP connection failed'));

        $this->expectException(ContactEmailFailedException::class);

        $this->useCase->execute($dto);
    }
}
