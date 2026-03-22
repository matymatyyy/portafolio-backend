<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Visit;

use App\Domain\Visit\Visit;
use App\Domain\Visit\VisitRepositoryInterface;
use DateTimeImmutable;
use PDO;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(VisitRepositoryInterface::class)]
final readonly class PdoVisitRepository implements VisitRepositoryInterface
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function save(Visit $visit): void
    {
        $sql = <<<'SQL'
            INSERT INTO page_visits (id, page, ip_address, user_agent, referrer, visited_at)
            VALUES (:id, :page, :ip_address, :user_agent, :referrer, :visited_at)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $visit->id()
                ->value(),
            'page' => $visit->page(),
            'ip_address' => $visit->ipAddress(),
            'user_agent' => $visit->userAgent(),
            'referrer' => $visit->referrer(),
            'visited_at' => $visit->visitedAt()
                ->format('Y-m-d H:i:s'),
        ]);
    }

    public function countTotal(?\DateTimeImmutable $since = null): int
    {
        $sql = 'SELECT COUNT(*) FROM page_visits';
        $params = [];

        if ($since !== null) {
            $sql .= ' WHERE visited_at >= :since';
            $params['since'] = $since->format('Y-m-d H:i:s');
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function countUniqueVisitors(?\DateTimeImmutable $since = null): int
    {
        $sql = "SELECT COUNT(DISTINCT ip_address) FROM page_visits WHERE ip_address IS NOT NULL AND ip_address != ''";
        $params = [];

        if ($since !== null) {
            $sql .= ' AND visited_at >= :since';
            $params['since'] = $since->format('Y-m-d H:i:s');
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return array<string, int>
     */
    public function countByPage(?\DateTimeImmutable $since = null): array
    {
        $sql = 'SELECT page, COUNT(*) as count FROM page_visits';
        $params = [];

        if ($since !== null) {
            $sql .= ' WHERE visited_at >= :since';
            $params['since'] = $since->format('Y-m-d H:i:s');
        }

        $sql .= ' GROUP BY page ORDER BY count DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        /** @var array<int, array{page: string, count: string}> $rows */
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['page']] = (int) $row['count'];
        }

        return $result;
    }

    /**
     * @return array<int, array{date: string, count: int}>
     */
    public function countByDay(int $days): array
    {
        $since = (new DateTimeImmutable())->modify('-' . $days . ' days')->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'SELECT DATE(visited_at) as date, COUNT(*) as count
             FROM page_visits
             WHERE visited_at >= :since
             GROUP BY DATE(visited_at)
             ORDER BY date ASC',
        );
        $stmt->execute([
            'since' => $since,
        ]);

        /** @var array<int, array{date: string, count: string}> $rows */
        $rows = $stmt->fetchAll();

        return array_map(static fn (array $row): array => [
            'date' => $row['date'],
            'count' => (int) $row['count'],
        ], $rows);
    }

    /**
     * @return array<int, array{referrer: string, count: int}>
     */
    public function countTopReferrers(int $limit): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT referrer, COUNT(*) as count
             FROM page_visits
             WHERE referrer IS NOT NULL AND referrer != ''
             GROUP BY referrer
             ORDER BY count DESC
             LIMIT :limit",
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        /** @var array<int, array{referrer: string, count: string}> $rows */
        $rows = $stmt->fetchAll();

        return array_map(static fn (array $row): array => [
            'referrer' => $row['referrer'],
            'count' => (int) $row['count'],
        ], $rows);
    }
}
