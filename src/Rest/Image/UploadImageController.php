<?php

declare(strict_types=1);

namespace App\Rest\Image;

use App\Converter\Image\ImageResponseConverter;
use App\Model\Response\ErrorResponse;
use App\Model\Response\Image\ImageResponse;
use App\Service\Image\UploadImageUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Images')]
final readonly class UploadImageController
{
    public function __construct(
        private UploadImageUseCase $uploadImageUseCase,
        private ImageResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/images', methods: ['POST'])]
    #[OA\Post(
        summary: 'Upload an image to cloud storage. Returns a public URL.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['file'],
                    properties: [
                        new OA\Property(
                            property: 'file',
                            type: 'string',
                            format: 'binary',
                            description: 'Image file (JPEG, PNG, GIF or WebP, max 5MB)'
                        ),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Image uploaded successfully',
                content: new OA\JsonContent(ref: new Model(type: ImageResponse::class)),
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid file format, size, or missing file',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        if ($file === null) {
            return new JsonResponse([
                'error' => 'validation_error',
                'message' => 'No file was uploaded. Please attach an image file.',
                'details' => [],
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $fileContent = file_get_contents($file->getPathname());

        if ($fileContent === false) {
            return new JsonResponse([
                'error' => 'internal_error',
                'message' => 'Could not read the uploaded file.',
                'details' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $dto = $this->uploadImageUseCase->execute(
            originalFilename: $file->getClientOriginalName(),
            mimeType: $file->getMimeType() ?? 'application/octet-stream',
            fileSize: $file->getSize(),
            fileContent: $fileContent,
        );

        return new JsonResponse($this->responseConverter->toResponse($dto), Response::HTTP_CREATED);
    }
}
