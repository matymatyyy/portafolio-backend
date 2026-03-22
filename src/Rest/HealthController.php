<?php

declare(strict_types=1);

namespace App\Rest;

use OpenApi\Attributes as OA;
use PDO;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class HealthController
{
    public function __construct(
        private PDO $pdo,
        private CacheItemPoolInterface $cache,
    ) {
    }

    #[Route('/health', methods: ['GET'])]
    #[OA\Get(
        summary: 'Health check endpoint',
        tags: ['Health'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Service is healthy',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                        new OA\Property(
                            property: 'services',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'database', type: 'string', example: 'ok'),
                                new OA\Property(property: 'cache', type: 'string', example: 'ok'),
                            ],
                        ),
                    ],
                ),
            ),
            new OA\Response(response: 503, description: 'Service is degraded'),
        ],
    )]
    public function __invoke(): JsonResponse
    {
        $services = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
        ];

        $allHealthy = !in_array('error', $services, true);

        return new JsonResponse([
            'status' => $allHealthy ? 'ok' : 'degraded',
            'services' => $services,
        ], $allHealthy ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE);
    }

    private function checkDatabase(): string
    {
        try {
            $this->pdo->query('SELECT 1');

            return 'ok';
        } catch (\Throwable) {
            return 'error';
        }
    }

    private function checkCache(): string
    {
        try {
            $item = $this->cache->getItem('health_check');
            $item->set('ok');
            $item->expiresAfter(10);
            $this->cache->save($item);

            return 'ok';
        } catch (\Throwable) {
            return 'error';
        }
    }
}
