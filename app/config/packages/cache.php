<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'cache' => [
            'app' => 'cache.adapter.redis',
            'default_redis_provider' => '%env(REDIS_DSN)%',
        ],
    ]);
};
