<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use PDO;

trait PaginatedQueryTrait
{
    private const array ALLOWED_TABLES = ['users', 'projects', 'page_visits', 'curriculum_vitae'];

    private const array ALLOWED_ORDER_COLUMNS = ['created_at', 'updated_at', 'uploaded_at', 'visited_at', 'name', 'title', 'email'];

    /**
     * @param array<string, array{column: string, operator: string}> $filterConfig
     * @param array<string, string> $filters
     * @return array{rows: array<int, array<string, mixed>>, total: int}
     */
    private function executePaginatedQuery(
        PDO $pdo,
        string $table,
        array $filterConfig,
        array $filters,
        int $page,
        int $limit,
        string $orderBy = 'created_at DESC',
    ): array {
        if (!in_array($table, self::ALLOWED_TABLES, true)) {
            throw new \InvalidArgumentException(sprintf('Table "%s" is not allowed for paginated queries.', $table));
        }

        $orderBy = $this->sanitizeOrderBy($orderBy);

        $where = [];
        $params = [];

        foreach ($filterConfig as $filterName => $config) {
            if (!isset($filters[$filterName]) || $filters[$filterName] === '') {
                continue;
            }

            if ($config['operator'] === 'LIKE') {
                $where[] = sprintf('%s LIKE :%s', $config['column'], $filterName);
                $params[$filterName] = '%' . $filters[$filterName] . '%';
            } else {
                $where[] = sprintf('%s %s :%s', $config['column'], $config['operator'], $filterName);
                $params[$filterName] = $filters[$filterName];
            }
        }

        $whereSql = $where !== [] ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $pdo->prepare(sprintf('SELECT COUNT(*) FROM %s %s', $table, $whereSql));
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $limit;
        $limit = min($limit, 100);

        $dataStmt = $pdo->prepare(
            sprintf('SELECT * FROM %s %s ORDER BY %s LIMIT :limit OFFSET :offset', $table, $whereSql, $orderBy),
        );

        foreach ($params as $key => $value) {
            $dataStmt->bindValue($key, $value);
        }

        $dataStmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $dataStmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        /** @var array<int, array<string, mixed>> $rows */
        $rows = $dataStmt->fetchAll();

        return [
            'rows' => $rows,
            'total' => $total,
        ];
    }

    private function sanitizeOrderBy(string $orderBy): string
    {
        $parts = explode(' ', trim($orderBy));
        $column = $parts[0] ?? 'created_at';
        $direction = strtoupper($parts[1] ?? 'DESC');

        if (!in_array($column, self::ALLOWED_ORDER_COLUMNS, true)) {
            $column = 'created_at';
        }

        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'DESC';
        }

        return $column . ' ' . $direction;
    }
}
