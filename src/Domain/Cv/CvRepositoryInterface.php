<?php

declare(strict_types=1);

namespace App\Domain\Cv;

interface CvRepositoryInterface
{
    public function save(Cv $cv): void;

    public function findActive(): ?Cv;

    public function deactivateAll(): void;
}
