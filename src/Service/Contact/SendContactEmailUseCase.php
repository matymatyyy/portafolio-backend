<?php

declare(strict_types=1);

namespace App\Service\Contact;

use App\Domain\Contact\Exception\ContactEmailFailedException;
use App\Domain\Contact\Service\ContactEmailServiceInterface;
use App\Service\Contact\DTO\SendContactEmailDTO;

final readonly class SendContactEmailUseCase
{
    public function __construct(
        private ContactEmailServiceInterface $contactEmailService,
    ) {
    }

    public function execute(SendContactEmailDTO $dto): void
    {
        try {
            $this->contactEmailService->sendContactEmail($dto->name, $dto->email, $dto->subject, $dto->message);
        } catch (\Throwable $e) {
            throw ContactEmailFailedException::because($e->getMessage());
        }
    }
}
