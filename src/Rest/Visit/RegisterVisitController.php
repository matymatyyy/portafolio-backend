<?php

declare(strict_types=1);

namespace App\Rest\Visit;

use App\Converter\Visit\VisitResponseConverter;
use App\Infrastructure\RateLimiter\RateLimiter;
use App\Model\Response\Visit\RegisterVisitResponse;
use App\Service\Visit\RegisterVisitUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Visits')]
final readonly class RegisterVisitController
{
    private const int MAX_PAGE_LENGTH = 500;

    public function __construct(
        private RegisterVisitUseCase $registerVisitUseCase,
        private VisitResponseConverter $responseConverter,
        private RateLimiter $rateLimiter,
    ) {
    }

    #[Route('/api/visits', methods: ['POST'])]
    #[OA\Post(
        summary: 'Register a page visit',
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'page',
                        type: 'string',
                        example: '/projects',
                        description: 'The page path visited'
                    ),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Visit registered successfully',
                content: new OA\JsonContent(ref: new Model(type: RegisterVisitResponse::class)),
            ),
            new OA\Response(response: 429, description: 'Too many requests'),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $clientIp = $request->getClientIp() ?? 'unknown';

        if (!$this->rateLimiter->isAllowed('visit_' . $clientIp, 60, 60)) {
            return new JsonResponse([
                'error' => 'rate_limited',
                'message' => 'Too many requests. Please try again later.',
                'details' => [],
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        /** @var array{page?: string}|null $body */
        $body = json_decode((string) $request->getContent(), true);

        $page = $this->sanitizePage($body['page'] ?? null);

        $userAgent = $request->headers->get('User-Agent');
        $referrer = $request->headers->get('Referer');

        $dto = $this->registerVisitUseCase->execute(
            page: $page,
            ipAddress: $request->getClientIp(),
            userAgent: $userAgent !== '' ? $userAgent : null,
            referrer: $referrer !== '' ? $referrer : null,
        );

        return new JsonResponse($this->responseConverter->toRegisterResponse($dto), Response::HTTP_CREATED);
    }

    private function sanitizePage(?string $page): string
    {
        if ($page === null || $page === '') {
            return '/';
        }

        if (!str_starts_with($page, '/')) {
            $page = '/' . $page;
        }

        if (mb_strlen($page) > self::MAX_PAGE_LENGTH) {
            $page = mb_substr($page, 0, self::MAX_PAGE_LENGTH);
        }

        return $page;
    }
}
