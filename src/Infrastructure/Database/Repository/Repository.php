<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use InvalidArgumentException;
use Iterator;
use RiverRing\Quest\Infrastructure\Database\Aggregator;
use RiverRing\Quest\Infrastructure\Database\Dbal\Driver\Driver;
use RiverRing\Quest\Infrastructure\Database\Repository\Specification\AggregateRootSpecification;
use RiverRing\Quest\Infrastructure\Database\Repository\Specification\EntitySpecification;
use RiverRing\Quest\Infrastructure\Database\Repository\Specification\PluralEntitySpecification;
use RiverRing\Quest\Infrastructure\Database\Repository\Specification\SingleEntitySpecification;

/**
 * @template T
 */
abstract class Repository
{
    private Aggregator $aggregator;
    private Driver $driver;

    public function __construct(Driver $driver, Aggregator $aggregator)
    {
        $this->aggregator = $aggregator;
        $this->driver = $driver;
    }

    abstract protected function specification(): AggregateRootSpecification;

    protected function findOne($sql, $params = []): ?array
    {
        return $this->driver->findOne($sql, $params);
    }

    protected function find($sql, $params = []): Iterator
    {
        return $this->driver->find($sql, $params);
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
            $aggregateRootSpecification->entitySpecifications(),
            $data[$aggregateRootSpecification->primaryKeyField()]
        );

        return $this->aggregator->aggregate($aggregateRootSpecification->className(), $data, $entities);
    }

    /**
     * @param EntitySpecification[] $entitySpecifications
     */
    protected function findEntities(array $entitySpecifications, int|string $aggregateRootId): array
    {
        $entities = [];
        foreach ($entitySpecifications as $specification) {
            $entities[$specification->className()] = match (true) {
                $specification instanceof SingleEntitySpecification => $this->driver->findEntity(
                    $aggregateRootId,
                    $specification->tableName(),
                    $specification->referencedField()
                ),
                $specification instanceof PluralEntitySpecification => $this->driver->findEntitySet(
                    $aggregateRootId,
                    $specification->tableName(),
                    $specification->referencedField()
                ),
                default => throw new InvalidArgumentException(sprintf('Unexpected entity specification class %s', get_class($specification))),
            };
        }

        return $entities;
    }
}