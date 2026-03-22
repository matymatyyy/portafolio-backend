<?php

declare(strict_types=1);

namespace App\Rest\Cv;

use App\Converter\Cv\CvResponseConverter;
use App\Model\Response\Cv\CvResponse;
use App\Model\Response\ErrorResponse;
use App\Service\Cv\GetActiveCvUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'CV')]
final readonly class GetActiveCvController
{
    public function __construct(
        private GetActiveCvUseCase $getActiveCvUseCase,
        private CvResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/cv/status', methods: ['GET'])]
    #[OA\Get(
        summary: 'Check if there is an active CV and return its metadata',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Active CV found',
                content: new OA\JsonContent(ref: new Model(type: CvResponse::class)),
            ),
            new OA\Response(
                response: 404,
                description: 'No active CV found',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(): JsonResponse
    {
        $dto = $this->getActiveCvUseCase->execute();

        if ($dto === null) {
            return new JsonResponse([
                'error' => 'not_found',
                'message' => 'No active CV found.',
                'details' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->responseConverter->toResponse($dto), Response::HTTP_OK);
    }
}
