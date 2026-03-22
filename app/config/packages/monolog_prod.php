<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('monolog', [
        'handlers' => [
            'main' => [
                'type' => 'fingers_crossed',
                'action_level' => 'error',
                'handler' => 'nested',
                'buffer_size' => 50,
                'excluded_http_codes' => [404, 405],
            ],
            'nested' => [
                'type' => 'stream',
                'path' => 'php://stderr',
                'level' => 'debug',
                'formatter' => 'monolog.formatter.json',
            ],
            'deprecation' => [
                'type' => 'stream',
                'channels' => ['deprecation'],
                'path' => 'php://stderr',
                'formatter' => 'monolog.formatter.json',
            ],
        ],
    ]);
};
