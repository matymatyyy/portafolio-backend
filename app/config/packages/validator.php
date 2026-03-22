<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'validation' => [
            'email_validation_mode' => 'html5',
        ],
    ]);

    if ($container->env() === 'test') {
        $container->extension('framework', [
            'validation' => [
                'not_compromised_password' => false,
            ],
        ]);
    }
};
