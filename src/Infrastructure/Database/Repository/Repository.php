<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use Iterator;
use PDO;
use RiverRing\Quest\Infrastructure\Database\Aggregator;
use RiverRing\Quest\Infrastructure\Database\PdoProvider;
use RuntimeException;

/**
 * @template T
 * @method array findEntities(mixed $id)
 */
abstract class Repository
{
    protected PDO $pdo;
    private Aggregator $aggregator;

    public function __construct(PdoProvider $pdoProvider, Aggregator $aggregator)
    {
        $this->pdo = $pdoProvider->provide();
        $this->aggregator = $aggregator;
        if(!method_exists($this,'findEntities')) {
            throw new RuntimeException('Each repository must contain an "findEntities" method with a protected access modifier.');
        }
    }

    abstract protected function specification(): AggregateRootSpecification;

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

    protected function execute($sql, $params = []): void
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * @return T|null
     */
    protected function aggregateOne(?array $data): ?object
    {
        if ($data === null) {
            return null;
        }

        $aggregateRootSpecification = $this->specification();

        $entities = $this->findEntities(
            $aggregateRootSpecification->aggregateRootIdFactory()(
                $data[$aggregateRootSpecification->primaryKeyFieldName()]
            )
        );

        return $this->aggregator->aggregate($aggregateRootSpecification->aggregateRootClassName(), $data, $entities);
    }

    protected function dump(object $aggregateRoot): array
    {

    }
}