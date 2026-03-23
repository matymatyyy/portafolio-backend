<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\User\Service\NotificationServiceInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;

#[AsAlias(NotificationServiceInterface::class)]
final readonly class SymfonyMailerNotificationService implements NotificationServiceInterface
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire(env: 'MAILER_FROM')]
        private string $mailerFrom,
    ) {
    }

    public function sendWelcomeEmail(string $email, string $name): void
    {
        $message = (new TemplatedEmail())
            ->from($this->mailerFrom)
            ->to($email)
            ->subject('Welcome to Portfolio!')
            ->htmlTemplate('user/welcome.html.twig')
            ->context([
                'userName' => $name,
                'userEmail' => $email,
            ]);

        $this->mailer->send($message);
    }
}
