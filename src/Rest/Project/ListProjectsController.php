<?php

declare(strict_types=1);

namespace App\Rest\Project;

use App\Converter\Project\ProjectResponseConverter;
use App\Model\Response\Project\PaginatedProjectResponse;
use App\Service\Project\ListProjectsUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Projects')]
final readonly class ListProjectsController
{
    public function __construct(
        private ListProjectsUseCase $listProjectsUseCase,
        private ProjectResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/projects', methods: ['GET'])]
    #[OA\Get(
        summary: 'List projects with pagination',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'title', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: [
                'active',
                'archived',
            ])),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of projects',
                content: new OA\JsonContent(ref: new Model(type: PaginatedProjectResponse::class)),
            ),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $filters = array_filter([
            'title' => $request->query->getString('title'),
            'status' => $request->query->getString('status'),
        ], static fn (string $value) => $value !== '');

        $result = $this->listProjectsUseCase->execute($page, $limit, $filters);

        return new JsonResponse($this->responseConverter->toPaginatedResponse($result));
    }
}
