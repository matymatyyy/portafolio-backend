<?php

declare(strict_types=1);

namespace App\Domain\Common;

interface DomainExceptionInterface extends \Throwable
{
    public function httpStatusCode(): int;

    public function errorCode(): string;
}
