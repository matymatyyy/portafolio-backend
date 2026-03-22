<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('nelmio_api_doc', [
        'documentation' => [
            'info' => [
                'title' => 'Portfolio API',
                'description' => 'Production-grade REST API built with Symfony and Hexagonal Architecture',
                'version' => '1.0.0',
            ],
            'components' => [
                'securitySchemes' => [
                    'Bearer' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                    ],
                ],
            ],
            'security' => [
                ['Bearer' => []],
            ],
        ],
        'areas' => [
            'path_patterns' => ['^/api(?!/doc)', '^/health'],
        ],
    ]);
};
