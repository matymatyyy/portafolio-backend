<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Contact\Service\ContactEmailServiceInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsAlias(ContactEmailServiceInterface::class)]
final readonly class SymfonyMailerContactService implements ContactEmailServiceInterface
{
    private const string RECIPIENT_EMAIL = 'matias@example.com';

    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function sendContactEmail(string $name, string $email, string $subject, string $message): void
    {
        $emailMessage = (new TemplatedEmail())
            ->from(new Address('noreply@portfolio.local', 'Portfolio Contact'))
            ->to(self::RECIPIENT_EMAIL)
            ->replyTo(new Address($email, $name))
            ->subject(sprintf('[Portfolio Contact] %s', $subject))
            ->htmlTemplate('contact/contact_email.html.twig')
            ->context([
                'senderName' => $name,
                'senderEmail' => $email,
                'subject' => $subject,
                'contactMessage' => $message,
            ]);

        $this->mailer->send($emailMessage);
    }
}
