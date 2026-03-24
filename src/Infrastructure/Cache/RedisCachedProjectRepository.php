<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Domain\Project\Project;
use App\Domain\Project\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\Slug;
use App\Infrastructure\Repository\Project\PdoProjectRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class RedisCachedProjectRepository implements ProjectRepositoryInterface
{
    private const int TTL = 3600;

    private const string LIST_VERSION_KEY = 'project_list_version';

    public function __construct(
        #[Autowire(service: PdoProjectRepository::class)]
        private ProjectRepositoryInterface $inner,
        private CacheItemPoolInterface $cache,
    ) {
    }

    public function save(Project $project): void
    {
        $this->inner->save($project);

        $this->invalidate($project);
        $this->invalidateList();
    }

    public function remove(Project $project): void
    {
        $this->inner->remove($project);

        $this->invalidate($project);
        $this->invalidateList();
    }

    public function findById(ProjectId $id): ?Project
    {
        $key = $this->idKey($id->value());

        try {
            $item = $this->cache->getItem($key);

            if ($item->isHit()) {
                /** @var Project */
                return $item->get();
            }
        } catch (\Throwable) {
            return $this->inner->findById($id);
        }

        $project = $this->inner->findById($id);

        if ($project !== null) {
            try {
                $item->set($project);
                $item->expiresAfter(self::TTL);
                $this->cache->save($item);
            } catch (\Throwable) {
            }
        }

        return $project;
    }

    public function findBySlug(Slug $slug): ?Project
    {
        $key = $this->slugKey($slug->value());

        try {
            $item = $this->cache->getItem($key);

            if ($item->isHit()) {
                /** @var Project */
                return $item->get();
            }
        } catch (\Throwable) {
            return $this->inner->findBySlug($slug);
        }

        $project = $this->inner->findBySlug($slug);

        if ($project !== null) {
            try {
                $item->set($project);
                $item->expiresAfter(self::TTL);
                $this->cache->save($item);
            } catch (\Throwable) {
            }
        }

        return $project;
    }

    /**
     * @param array<string, string> $filters
     * @return array{items: Project[], total: int}
     */
    public function findPaginated(int $page, int $limit, array $filters = []): array
    {
        $key = $this->listKey($page, $limit, $filters);

        try {
            $item = $this->cache->getItem($key);

            if ($item->isHit()) {
                /** @var array{items: Project[], total: int} */
                return $item->get();
            }
        } catch (\Throwable) {
            return $this->inner->findPaginated($page, $limit, $filters);
        }

        $result = $this->inner->findPaginated($page, $limit, $filters);

        try {
            $item->set($result);
            $item->expiresAfter(self::TTL);
            $this->cache->save($item);
        } catch (\Throwable) {
        }

        return $result;
    }

    private function invalidate(Project $project): void
    {
        try {
            $this->cache->deleteItems([
                $this->idKey($project->id()->value()),
                $this->slugKey($project->slug()->value()),
            ]);
        } catch (\Throwable) {
        }
    }

    private function invalidateList(): void
    {
        try {
            $this->cache->deleteItem(self::LIST_VERSION_KEY);
        } catch (\Throwable) {
        }
    }

    private function idKey(string $id): string
    {
        return 'project_id_' . $id;
    }

    private function slugKey(string $slug): string
    {
        return 'project_slug_' . $slug;
    }

    private function listVersion(): string
    {
        try {
            $item = $this->cache->getItem(self::LIST_VERSION_KEY);

            if ($item->isHit()) {
                /** @var string */
                return $item->get();
            }

            $version = bin2hex(random_bytes(8));
            $item->set($version);
            $item->expiresAfter(self::TTL);
            $this->cache->save($item);

            return $version;
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * @param array<string, string> $filters
     */
    private function listKey(int $page, int $limit, array $filters): string
    {
        return 'project_list_' . $this->listVersion() . '_' . md5(serialize([$page, $limit, $filters]));
    }
}
