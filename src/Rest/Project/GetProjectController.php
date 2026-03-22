<?php

declare(strict_types=1);

namespace App\Rest\Project;

use App\Converter\Project\ProjectResponseConverter;
use App\Model\Response\ErrorResponse;
use App\Model\Response\Project\ProjectResponse;
use App\Service\Project\GetProjectUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
final readonly class GetProjectController
{
    public function __construct(
        private GetProjectUseCase $getProjectUseCase,
        private ProjectResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/projects/{id}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get a project by ID',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Project details',
                content: new OA\JsonContent(ref: new Model(type: ProjectResponse::class)),
            ),
            new OA\Response(
                response: 404,
                description: 'Project not found',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(string $id): JsonResponse
    {
        $projectDTO = $this->getProjectUseCase->execute($id);

        return new JsonResponse($this->responseConverter->toResponse($projectDTO));
    }
}
