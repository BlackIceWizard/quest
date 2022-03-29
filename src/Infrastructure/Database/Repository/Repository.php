<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use InvalidArgumentException;
use Iterator;
use RiverRing\Quest\Infrastructure\Database\Aggregator;
use RiverRing\Quest\Infrastructure\Database\Dbal\Driver\Driver;
use RiverRing\Quest\Infrastructure\Database\Dumper;
use RiverRing\Quest\Infrastructure\Database\Mapping\MapperRegistry;
use RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation\RawData;
use RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation\Record;
use RiverRing\Quest\Infrastructure\Database\Specification\AggregateRootSpecification;
use RiverRing\Quest\Infrastructure\Database\Specification\EntitySpecification;
use RiverRing\Quest\Infrastructure\Database\Specification\PluralEntitySpecification;
use RiverRing\Quest\Infrastructure\Database\Specification\SingleEntitySpecification;

/**
 * @template T
 */
abstract class Repository
{
    private Driver $driver;
    private Aggregator $aggregator;
    private Dumper $dumper;

    public function __construct(Driver $driver, MapperRegistry $mappers)
    {
        $this->driver = $driver;
        $this->aggregator = new Aggregator($mappers);
        $this->dumper = new Dumper($mappers);
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
     * @param EntitySpecification[] $entitySpecifications
     */
    private function findEntities(array $entitySpecifications, int|string $aggregateRootId): array
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

    /**
     * @return T|null
     */
    protected function aggregateOne(?array $aggregateRootData): ?object
    {
        if ($aggregateRootData === null) {
            return null;
        }

        $aggregateRootSpecification = $this->specification();

        $entitiesData = $this->findEntities(
            $aggregateRootSpecification->entitySpecifications(),
            $aggregateRootData[$aggregateRootSpecification->primaryKeyField()]
        );

        $allData = [$aggregateRootSpecification->className() => $aggregateRootData] + $entitiesData;

        return $this->aggregator->aggregate(
            $aggregateRootSpecification,
            new RawData(
                array_combine(
                    array_keys($allData),
                    array_map(
                        static function ($recordData) {
                            if ($recordData === null) {
                                return null;
                            }

                            if ($recordData instanceof Iterator) {
                                return array_map(
                                    fn($data): Record => Record::justLoaded($data),
                                    iterator_to_array($recordData)
                                );
                            }

                            return Record::justLoaded($recordData);
                        },
                        $allData
                    )
                )
            )
        );
    }


    public function store(object $aggregateRoot): void
    {
        $rawData = $this->dumper->dump($this->specification(), $aggregateRoot);

        var_export($rawData);
        exit;

        $this->driver->store($rawData);
    }
}