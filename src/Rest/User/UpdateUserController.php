<?php

declare(strict_types=1);

namespace App\Rest\User;

use App\Converter\User\UpdateUserRequestConverter;
use App\Converter\User\UserResponseConverter;
use App\Model\Request\User\UpdateUserRequest;
use App\Model\Response\ErrorResponse;
use App\Model\Response\User\UserResponse;
use App\Service\User\UpdateUserUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Users')]
final readonly class UpdateUserController
{
    public function __construct(
        private UpdateUserUseCase $updateUserUseCase,
        private UpdateUserRequestConverter $requestConverter,
        private UserResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/users/{id}', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Update a user',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: UpdateUserRequest::class)),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User updated successfully',
                content: new OA\JsonContent(ref: new Model(type: UserResponse::class)),
            ),
            new OA\Response(
                response: 404,
                description: 'User not found',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(string $id, #[MapRequestPayload] UpdateUserRequest $request): JsonResponse
    {
        $dto = $this->requestConverter->fromRequest($id, $request);
        $userDTO = $this->updateUserUseCase->execute($dto);

        return new JsonResponse($this->responseConverter->toResponse($userDTO));
    }
}
