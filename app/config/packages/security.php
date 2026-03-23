<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('security', [
        'password_hashers' => [
            'Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface' => 'auto',
        ],
        'providers' => [
            'app_user_provider' => [
                'id' => 'App\Infrastructure\Security\UserProvider',
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'login' => [
                'pattern' => '^/api/login$',
                'stateless' => true,
                'json_login' => [
                    'check_path' => '/api/login',
                    'success_handler' => 'lexik_jwt_authentication.handler.authentication_success',
                    'failure_handler' => 'lexik_jwt_authentication.handler.authentication_failure',
                ],
            ],
            'api' => [
                'pattern' => '^/api',
                'stateless' => true,
                'jwt' => [],
            ],
        ],
        'access_control' => [
            ['path' => '^/api/doc', 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/api/login', 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/api/users$', 'methods' => ['POST'], 'roles' => 'IS_AUTHENTICATED_FULLY'],
            ['path' => '^/api/visits$', 'methods' => ['POST'], 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/api/projects', 'methods' => ['GET'], 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/api/contact$', 'methods' => ['POST'], 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/api/cv$', 'methods' => ['GET'], 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/api/cv/status$', 'methods' => ['GET'], 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/health', 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/api', 'roles' => 'IS_AUTHENTICATED_FULLY'],
        ],
    ]);

    if ($container->env() === 'test') {
        $container->extension('security', [
            'firewalls' => [
                'api' => [
                    'pattern' => '^/api',
                    'stateless' => true,
                    'security' => false,
                ],
            ],
        ]);
    }
};
