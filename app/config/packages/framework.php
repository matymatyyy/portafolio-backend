<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'secret' => '%env(APP_SECRET)%',
        'http_method_override' => false,
        'handle_all_throwables' => true,
        'php_errors' => [
            'log' => true,
        ],
        'serializer' => [
            'enabled' => true,
        ],
        'validation' => [
            'enabled' => true,
            'enable_attributes' => true,
        ],
        'property_access' => [
            'enabled' => true,
        ],
    ]);

    if ($container->env() === 'test') {
        $container->extension('framework', [
            'test' => true,
        ]);
    }
};
