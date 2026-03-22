<?php

declare(strict_types=1);

namespace App\Rest\Project;

use App\Converter\Project\ProjectResponseConverter;
use App\Converter\Project\UpdateProjectRequestConverter;
use App\Model\Request\Project\UpdateProjectRequest;
use App\Model\Response\ErrorResponse;
use App\Model\Response\Project\ProjectResponse;
use App\Service\Project\UpdateProjectUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
final readonly class UpdateProjectController
{
    public function __construct(
        private UpdateProjectUseCase $updateProjectUseCase,
        private UpdateProjectRequestConverter $requestConverter,
        private ProjectResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/projects/{id}', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Update a project',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: UpdateProjectRequest::class)),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Project updated successfully',
                content: new OA\JsonContent(ref: new Model(type: ProjectResponse::class)),
            ),
            new OA\Response(
                response: 404,
                description: 'Project not found',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(string $id, #[MapRequestPayload] UpdateProjectRequest $request): JsonResponse
    {
        $dto = $this->requestConverter->fromRequest($id, $request);
        $projectDTO = $this->updateProjectUseCase->execute($dto);

        return new JsonResponse($this->responseConverter->toResponse($projectDTO));
    }
}
