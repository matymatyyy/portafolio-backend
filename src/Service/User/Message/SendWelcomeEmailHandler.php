<?php

declare(strict_types=1);

namespace App\Service\User\Message;

use App\Domain\User\Service\NotificationServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendWelcomeEmailHandler
{
    public function __construct(
        private NotificationServiceInterface $notificationService,
    ) {
    }

    public function __invoke(SendWelcomeEmailMessage $message): void
    {
        $this->notificationService->sendWelcomeEmail($message->email, $message->name);
    }
}
