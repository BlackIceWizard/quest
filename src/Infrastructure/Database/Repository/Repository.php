<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use Iterator;
use JetBrains\PhpStorm\Pure;
use PDO;
use RiverRing\Quest\Domain\AggregateRootId;
use RiverRing\Quest\Infrastructure\Database\Aggregator;
use RiverRing\Quest\Infrastructure\Database\PdoProvider;

/**
 * @template T
 */
abstract class Repository
{
    protected PDO $pdo;
    private Aggregator $aggregator;

    #[Pure]
    public function __construct(PdoProvider $pdoProvider, Aggregator $aggregator)
    {
        $this->pdo = $pdoProvider->provide();
        $this->aggregator = $aggregator;
    }

    /**
     * @return class-string<T>
     */
    abstract protected function aggregateRootClass(): string;

    abstract public function primaryKey(): PrimaryKeySpecification;

    abstract protected function findEntities(AggregateRootId $id): array;

    protected function findOne($sql, $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($row = $stmt->fetch()) {
            return $row;
        }

        return null;
    }

    protected function find($sql, $params = []): Iterator
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->getIterator();
    }

    /**
     * @return T|null
     */
    protected function aggregateOne(?array $data): ?object
    {
        if ($data === null) {
            return null;
        }

        $primaryKeySpecification = $this->primaryKey();

        $entities = $this->findEntities(
            $primaryKeySpecification->className()::fromString(
                $data[$primaryKeySpecification->fieldName()]
            )
        );

        return $this->aggregator->aggregate($this->aggregateRootClass(), $data, $entities);
    }
}