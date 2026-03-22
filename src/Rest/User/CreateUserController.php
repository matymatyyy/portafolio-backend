<?php

declare(strict_types=1);

namespace App\Rest\User;

use App\Converter\User\CreateUserRequestConverter;
use App\Converter\User\UserResponseConverter;
use App\Model\Request\User\CreateUserRequest;
use App\Model\Response\ErrorResponse;
use App\Model\Response\User\UserResponse;
use App\Service\User\CreateUserUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Users')]
final readonly class CreateUserController
{
    public function __construct(
        private CreateUserUseCase $createUserUseCase,
        private CreateUserRequestConverter $requestConverter,
        private UserResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/users', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateUserRequest::class)),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User created successfully',
                content: new OA\JsonContent(ref: new Model(type: UserResponse::class)),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
            new OA\Response(
                response: 409,
                description: 'User already exists',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(#[MapRequestPayload] CreateUserRequest $request): JsonResponse
    {
        $dto = $this->requestConverter->fromRequest($request);
        $userDTO = $this->createUserUseCase->execute($dto);

        return new JsonResponse($this->responseConverter->toResponse($userDTO), Response::HTTP_CREATED);
    }
}
