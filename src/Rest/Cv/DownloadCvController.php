<?php

declare(strict_types=1);

namespace App\Rest\Cv;

use App\Model\Response\ErrorResponse;
use App\Service\Cv\DownloadCvUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'CV')]
final readonly class DownloadCvController
{
    public function __construct(
        private DownloadCvUseCase $downloadCvUseCase,
    ) {
    }

    #[Route('/api/cv', methods: ['GET'])]
    #[OA\Get(
        summary: 'Download the active CV',
        responses: [
            new OA\Response(
                response: 200,
                description: 'CV file download',
                content: new OA\MediaType(
                    mediaType: 'application/octet-stream',
                    schema: new OA\Schema(type: 'string', format: 'binary'),
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'No active CV found',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(): Response
    {
        $cvFile = $this->downloadCvUseCase->execute();

        return new Response($cvFile->fileContent, Response::HTTP_OK, [
            'Content-Type' => $cvFile->mimeType,
            'Content-Disposition' => sprintf('attachment; filename="%s"', $cvFile->originalFilename),
            'Content-Length' => (string) $cvFile->fileSize,
        ]);
    }
}
