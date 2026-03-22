<?php

declare(strict_types=1);

namespace App\Domain\Contact\Service;

interface ContactEmailServiceInterface
{
    public function sendContactEmail(string $name, string $email, string $subject, string $message): void;
}
