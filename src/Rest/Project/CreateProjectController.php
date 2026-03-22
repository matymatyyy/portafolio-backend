<?php

declare(strict_types=1);

namespace App\Rest\Project;

use App\Converter\Project\CreateProjectRequestConverter;
use App\Converter\Project\ProjectResponseConverter;
use App\Model\Request\Project\CreateProjectRequest;
use App\Model\Response\ErrorResponse;
use App\Model\Response\Project\ProjectResponse;
use App\Service\Project\CreateProjectUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
final readonly class CreateProjectController
{
    public function __construct(
        private CreateProjectUseCase $createProjectUseCase,
        private CreateProjectRequestConverter $requestConverter,
        private ProjectResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/projects', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create a new project',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateProjectRequest::class)),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Project created successfully',
                content: new OA\JsonContent(ref: new Model(type: ProjectResponse::class)),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
            new OA\Response(
                response: 409,
                description: 'Project already exists',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(#[MapRequestPayload] CreateProjectRequest $request): JsonResponse
    {
        $dto = $this->requestConverter->fromRequest($request);
        $projectDTO = $this->createProjectUseCase->execute($dto);

        return new JsonResponse($this->responseConverter->toResponse($projectDTO), Response::HTTP_CREATED);
    }
}
