<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsAlias(\PDO::class)]
final class PdoConnectionFactory extends \PDO
{
    public function __construct(#[Autowire(env: 'DATABASE_URL')] string $databaseUrl)
    {
        $params = parse_url($databaseUrl);

        if ($params === false) {
            throw new \RuntimeException('Invalid DATABASE_URL format.');
        }

        $queryParams = [];
        if (isset($params['query'])) {
            parse_str($params['query'], $queryParams);
        }

        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s',
            $params['host'] ?? 'localhost',
            $params['port'] ?? 5432,
            ltrim($params['path'] ?? '', '/'),
        );

        if (isset($queryParams['sslmode'])) {
            $dsn .= ';sslmode=' . (string) $queryParams['sslmode'];
        }

        parent::__construct(
            $dsn,
            $params['user'] ?? '',
            $params['pass'] ?? '',
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
    }
}
