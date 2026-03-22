<?php

declare(strict_types=1);

namespace App\Infrastructure\Transaction;

use App\Domain\Common\TransactionManagerInterface;
use PDO;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(TransactionManagerInterface::class)]
final readonly class PdoTransactionManager implements TransactionManagerInterface
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function transactional(callable $operation): mixed
    {
        $this->pdo->beginTransaction();

        try {
            $result = $operation();
            $this->pdo->commit();

            return $result;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();

            throw $e;
        }
    }
}
