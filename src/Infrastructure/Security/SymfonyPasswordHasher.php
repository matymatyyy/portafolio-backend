<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\User\Service\PasswordHasherInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

#[AsAlias(PasswordHasherInterface::class)]
final readonly class SymfonyPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private PasswordHasherFactoryInterface $hasherFactory,
    ) {
    }

    public function hash(string $plainPassword): string
    {
        $hasher = $this->hasherFactory->getPasswordHasher(SecurityUser::class);

        return $hasher->hash($plainPassword);
    }
}
