<?php

declare(strict_types=1);

use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Cache\RedisCachedProjectRepository;
use App\Infrastructure\Cache\RedisCachedUserRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure();

    $services->load('App\\', '%kernel.project_dir%/src/')
        ->exclude('%kernel.project_dir%/src/Infrastructure/Symfony/Kernel.php');

    $services->load('App\\Rest\\', '%kernel.project_dir%/src/Rest/')
        ->tag('controller.service_arguments');

    $services->alias(UserRepositoryInterface::class, RedisCachedUserRepository::class);
    $services->alias(ProjectRepositoryInterface::class, RedisCachedProjectRepository::class);
};
