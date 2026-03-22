<?php

declare(strict_types=1);

use App\Service\User\Message\SendWelcomeEmailMessage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'messenger' => [
            'failure_transport' => 'failed',
            'transports' => [
                'async' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'retry_strategy' => [
                        'max_retries' => 3,
                        'delay' => 1000,
                        'multiplier' => 2,
                        'max_delay' => 0,
                    ],
                ],
                'failed' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                ],
            ],
            'routing' => [
                SendWelcomeEmailMessage::class => 'async',
            ],
        ],
    ]);
};
