<?php

declare(strict_types=1);

namespace App\Rest\User;

use App\Converter\User\UserResponseConverter;
use App\Model\Response\ErrorResponse;
use App\Model\Response\User\UserResponse;
use App\Service\User\GetUserUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Users')]
final readonly class GetUserController
{
    public function __construct(
        private GetUserUseCase $getUserUseCase,
        private UserResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/users/{id}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get a user by ID',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User details',
                content: new OA\JsonContent(ref: new Model(type: UserResponse::class)),
            ),
            new OA\Response(
                response: 404,
                description: 'User not found',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(string $id): JsonResponse
    {
        $userDTO = $this->getUserUseCase->execute($id);

        return new JsonResponse($this->responseConverter->toResponse($userDTO));
    }
}
