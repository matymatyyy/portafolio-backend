<?php

declare(strict_types=1);

namespace App\Tests\Service\User\Message;

use App\Domain\User\Service\NotificationServiceInterface;
use App\Service\User\Message\SendWelcomeEmailHandler;
use App\Service\User\Message\SendWelcomeEmailMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SendWelcomeEmailHandlerTest extends TestCase
{
    private NotificationServiceInterface&MockObject $notificationService;

    private SendWelcomeEmailHandler $handler;

    protected function setUp(): void
    {
        $this->notificationService = $this->createMock(NotificationServiceInterface::class);
        $this->handler = new SendWelcomeEmailHandler($this->notificationService);
    }

    #[Test]
    public function itSendsWelcomeEmail(): void
    {
        $message = new SendWelcomeEmailMessage('john@example.com', 'John Doe');

        $this->notificationService->expects(self::once())
            ->method('sendWelcomeEmail')
            ->with('john@example.com', 'John Doe');

        ($this->handler)($message);
    }
}
