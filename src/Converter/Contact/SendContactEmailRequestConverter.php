<?php

declare(strict_types=1);

namespace App\Converter\Contact;

use App\Model\Request\Contact\SendContactEmailRequest;
use App\Service\Contact\DTO\SendContactEmailDTO;

final class SendContactEmailRequestConverter
{
    public function fromRequest(SendContactEmailRequest $request): SendContactEmailDTO
    {
        return new SendContactEmailDTO(
            name: $request->name,
            email: $request->email,
            subject: $request->subject,
            message: $request->message,
        );
    }
}
