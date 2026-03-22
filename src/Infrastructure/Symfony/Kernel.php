<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return dirname(__DIR__, 3);
    }

    public function getConfigDir(): string
    {
        return $this->getProjectDir() . '/app/config';
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/app/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/app/var/log';
    }
}
