<?php

declare(strict_types=1);

namespace App\Rest\Project;

use App\Model\Response\ErrorResponse;
use App\Service\Project\DeleteProjectUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
final readonly class DeleteProjectController
{
    public function __construct(
        private DeleteProjectUseCase $deleteProjectUseCase,
    ) {
    }

    #[Route('/api/projects/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a project',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Project deleted successfully'),
            new OA\Response(
                response: 404,
                description: 'Project not found',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(string $id): JsonResponse
    {
        $this->deleteProjectUseCase->execute($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
