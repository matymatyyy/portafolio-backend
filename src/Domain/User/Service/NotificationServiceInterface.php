<?php

declare(strict_types=1);

namespace App\Domain\User\Service;

interface NotificationServiceInterface
{
    public function sendWelcomeEmail(string $email, string $name): void;
}
