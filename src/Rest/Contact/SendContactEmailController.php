<?php

declare(strict_types=1);

namespace App\Rest\Contact;

use App\Converter\Contact\SendContactEmailRequestConverter;
use App\Infrastructure\RateLimiter\RateLimiter;
use App\Model\Request\Contact\SendContactEmailRequest;
use App\Model\Response\Contact\SendContactEmailResponse;
use App\Model\Response\ErrorResponse;
use App\Service\Contact\SendContactEmailUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Contact')]
final readonly class SendContactEmailController
{
    public function __construct(
        private SendContactEmailUseCase $sendContactEmailUseCase,
        private SendContactEmailRequestConverter $requestConverter,
        private RateLimiter $rateLimiter,
    ) {
    }

    #[Route('/api/contact', methods: ['POST'])]
    #[OA\Post(
        summary: 'Send a contact email',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: SendContactEmailRequest::class)),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Contact email sent successfully',
                content: new OA\JsonContent(ref: new Model(type: SendContactEmailResponse::class)),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
            new OA\Response(response: 429, description: 'Too many requests'),
            new OA\Response(
                response: 500,
                description: 'Failed to send email',
                content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class)),
            ),
        ],
    )]
    public function __invoke(
        Request $httpRequest,
        #[MapRequestPayload]
        SendContactEmailRequest $request,
    ): JsonResponse {
        $clientIp = $httpRequest->getClientIp() ?? 'unknown';

        if (!$this->rateLimiter->isAllowed('contact_' . $clientIp, 5, 300, failOpen: false)) {
            return new JsonResponse([
                'error' => 'rate_limited',
                'message' => 'Too many contact requests. Please try again later.',
                'details' => [],
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $dto = $this->requestConverter->fromRequest($request);
        $this->sendContactEmailUseCase->execute($dto);

        $response = new SendContactEmailResponse(message: 'Contact email sent successfully.');

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
