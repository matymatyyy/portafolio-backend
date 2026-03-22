<?php

declare(strict_types=1);

namespace App\Domain\Project;

enum Status: string
{
    case Active = 'active';
    case Archived = 'archived';
}
