<?php

declare(strict_types=1);

namespace App\Domain\Common;

interface FileStorageInterface
{
    public function upload(string $content, string $key, string $mimeType): string;

    public function delete(string $key): void;
}
