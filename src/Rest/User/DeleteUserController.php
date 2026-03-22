<?php

declare(strict_types=1);

namespace App\Rest\User;

use App\Model\Response\ErrorResponse;
use App\Service\User\DeleteUserUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Users')]
final readonly class DeleteUserController
{
    public function __construct(
        private DeleteUserUseCase $deleteUserUseCase,
    ) {
    }

    #[Route('/api/users/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a user',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'User deleted successfully'),
            new OA\Response(
                response: 404,
                description: 'User not found',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(string $id): JsonResponse
    {
        $this->deleteUserUseCase->execute($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
