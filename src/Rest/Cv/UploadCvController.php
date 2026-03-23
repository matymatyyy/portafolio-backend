<?php

declare(strict_types=1);

namespace App\Rest\Cv;

use App\Converter\Cv\CvResponseConverter;
use App\Model\Response\Cv\CvResponse;
use App\Model\Response\ErrorResponse;
use App\Service\Cv\UploadCvUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'CV')]
final readonly class UploadCvController
{
    public function __construct(
        private UploadCvUseCase $uploadCvUseCase,
        private CvResponseConverter $responseConverter,
    ) {
    }

    #[Route('/api/cv', methods: ['POST'])]
    #[OA\Post(
        summary: 'Upload a CV (PDF or Word). Deactivates any existing active CV.',
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
                            description: 'CV file (PDF, DOC or DOCX)'
                        ),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'CV uploaded successfully',
                content: new OA\JsonContent(ref: new Model(type: CvResponse::class)),
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid file format or missing file',
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
                'message' => 'No file was uploaded. Please attach a CV file.',
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

        $dto = $this->uploadCvUseCase->execute(
            originalFilename: $file->getClientOriginalName(),
            mimeType: $file->getMimeType() ?? 'application/octet-stream',
            fileSize: $file->getSize(),
            fileContent: $fileContent,
        );

        return new JsonResponse($this->responseConverter->toResponse($dto), Response::HTTP_CREATED);
    }
}
