<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Common\Exception\FileStorageException;
use App\Domain\Common\FileStorageInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsAlias(FileStorageInterface::class)]
final readonly class SupabaseFileStorage implements FileStorageInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'SUPABASE_URL')]
        private string $supabaseUrl,
        #[Autowire(env: 'SUPABASE_SERVICE_KEY')]
        private string $serviceKey,
        #[Autowire(env: 'SUPABASE_BUCKET')]
        private string $bucket,
    ) {
    }

    public function upload(string $content, string $key, string $mimeType): string
    {
        try {
            $response = $this->httpClient->request('POST', $this->storageUrl($key), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                    'Content-Type' => $mimeType,
                ],
                'body' => $content,
            ]);

            if ($response->getStatusCode() >= 400) {
                throw new \RuntimeException($response->getContent(false));
            }
        } catch (FileStorageException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw FileStorageException::uploadFailed($e->getMessage());
        }

        return $this->publicUrl($key);
    }

    public function delete(string $key): void
    {
        try {
            $response = $this->httpClient->request('DELETE', $this->storageUrl($key), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->serviceKey,
                ],
            ]);

            if ($response->getStatusCode() >= 400) {
                throw new \RuntimeException($response->getContent(false));
            }
        } catch (FileStorageException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw FileStorageException::deleteFailed($key, $e->getMessage());
        }
    }

    private function storageUrl(string $key): string
    {
        return rtrim($this->supabaseUrl, '/') . '/storage/v1/object/' . $this->bucket . '/' . $key;
    }

    private function publicUrl(string $key): string
    {
        return rtrim($this->supabaseUrl, '/') . '/storage/v1/object/public/' . $this->bucket . '/' . $key;
    }
}
