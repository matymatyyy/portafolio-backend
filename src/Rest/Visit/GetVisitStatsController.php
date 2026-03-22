<?php

declare(strict_types=1);

namespace App\Rest\Visit;

use App\Converter\Visit\VisitResponseConverter;
use App\Model\Response\Visit\VisitStatsResponse;
use App\Service\Visit\GetVisitStatsUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Visits')]
final readonly class GetVisitStatsController
{
    public function __construct(
        private GetVisitStatsUseCase $getVisitStatsUseCase,
        private VisitResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/visits/stats', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get visit statistics',
        parameters: [
            new OA\Parameter(
                name: 'days',
                in: 'query',
                description: 'Number of days for the daily breakdown (1-365)',
                schema: new OA\Schema(type: 'integer', default: 30),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Visit statistics',
                content: new OA\JsonContent(ref: new Model(type: VisitStatsResponse::class)),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $days = $request->query->getInt('days', 30);

        $dto = $this->getVisitStatsUseCase->execute($days);

        return new JsonResponse($this->responseConverter->toStatsResponse($dto));
    }
}
