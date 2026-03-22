<?php

declare(strict_types=1);

namespace App\Model\Request\Contact;

use Symfony\Component\Validator\Constraints as Assert;

final class SendContactEmailRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name is required.')]
        #[Assert\Length(min: 2, max: 100, minMessage: 'Name must be at least {{ limit }} characters.')]
        public readonly string $name = '',
        #[Assert\NotBlank(message: 'Email is required.')]
        #[Assert\Email(message: 'Please provide a valid email address.')]
        public readonly string $email = '',
        #[Assert\NotBlank(message: 'Subject is required.')]
        #[Assert\Length(min: 2, max: 200, minMessage: 'Subject must be at least {{ limit }} characters.')]
        public readonly string $subject = '',
        #[Assert\NotBlank(message: 'Message is required.')]
        #[Assert\Length(min: 10, max: 5000, minMessage: 'Message must be at least {{ limit }} characters.')]
        public readonly string $message = '',
    ) {
    }
}
