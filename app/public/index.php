<?php

declare(strict_types=1);

use App\Infrastructure\Symfony\Kernel;

require_once dirname(__DIR__, 2) . '/vendor/autoload_runtime.php';

return static function (array $context): Kernel {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
