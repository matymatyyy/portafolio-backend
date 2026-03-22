<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use App\Domain\Common\DomainExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: KernelEvents::EXCEPTION, priority: 10)]
final readonly class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->logger->error($exception->getMessage(), [
            'exception' => $exception::class,
            'trace' => $exception->getTraceAsString(),
        ]);

        $response = match (true) {
            $exception instanceof DomainExceptionInterface => $this->createResponse(
                $exception->errorCode(),
                $exception->getMessage(),
                $exception->httpStatusCode(),
            ),
            $exception instanceof AccessDeniedException,
            $exception instanceof AuthenticationException => $this->createResponse(
                'unauthorized',
                'Authentication is required to access this resource.',
                Response::HTTP_UNAUTHORIZED,
            ),
            $exception instanceof HttpExceptionInterface => $this->createResponse(
                'http_error',
                $exception->getMessage(),
                $exception->getStatusCode(),
            ),
            $exception->getPrevious() instanceof ValidationFailedException => $this->handleValidationException(
                $exception->getPrevious(),
            ),
            default => $this->createResponse(
                'internal_error',
                'An internal server error occurred.',
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ),
        };

        $event->setResponse($response);
    }

    private function handleValidationException(ValidationFailedException $exception): JsonResponse
    {
        $details = [];

        foreach ($exception->getViolations() as $violation) {
            $details[] = [
                'field' => $violation->getPropertyPath(),
                'message' => (string) $violation->getMessage(),
            ];
        }

        return $this->createResponse(
            'validation_error',
            'Invalid request data.',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $details,
        );
    }

    /**
     * @param array<int, array<string, string>> $details
     */
    private function createResponse(
        string $error,
        string $message,
        int $statusCode,
        array $details = [],
    ): JsonResponse {
        return new JsonResponse([
            'error' => $error,
            'message' => $message,
            'details' => $details,
        ], $statusCode);
    }
}
