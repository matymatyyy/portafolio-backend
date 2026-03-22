<?php

declare(strict_types=1);

namespace App\Rest\User;

use App\Converter\User\UserResponseConverter;
use App\Model\Response\User\PaginatedResponse;
use App\Service\User\ListUsersUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Users')]
final readonly class ListUsersController
{
    public function __construct(
        private ListUsersUseCase $listUsersUseCase,
        private UserResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/users', methods: ['GET'])]
    #[OA\Get(
        summary: 'List users with pagination',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'name', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'email', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of users',
                content: new OA\JsonContent(ref: new Model(type: PaginatedResponse::class)),
            ),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $filters = array_filter([
            'name' => $request->query->getString('name'),
            'email' => $request->query->getString('email'),
        ], static fn (string $value) => $value !== '');

        $result = $this->listUsersUseCase->execute($page, $limit, $filters);

        return new JsonResponse($this->responseConverter->toPaginatedResponse($result));
    }
}
