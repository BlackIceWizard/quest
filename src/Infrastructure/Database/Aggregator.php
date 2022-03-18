<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database;

use Iterator;
use RiverRing\Quest\Infrastructure\Database\Mapping\MapperRegistry;

/**
 * @template T
 */
class Aggregator
{
    private MapperRegistry $mappers;

    public function __construct(MapperRegistry $mappers)
    {
        $this->mappers = $mappers;
    }

    /**
     * @param class-string<T> $aggregateRootClassName
     * @return T
     */
    public function aggregate(string $aggregateRootClassName, array $aggregateRootData, array $entitiesData): object
    {
        $aggregateRootMapper = $this->mappers->byClassName($aggregateRootClassName);

        return $aggregateRootMapper->map(
            $aggregateRootData,
            $this->mapEntities($entitiesData)
        );
    }

    protected function mapEntities(array $entitiesData): array
    {
        $hydrated = [];
        foreach ($entitiesData as $className => $entityData) {
            if ($entityData === null) {
                $hydrated[$className] = null;
                continue;
            }

            $mapper = $this->mappers->byClassName($className);

            if ($entityData instanceof Iterator) {
                $hydrated[$className] = [];
                foreach ($entityData as $entityListItemData) {
                    $hydrated[$className][] = $mapper->map($entityListItemData);
                }
                continue;
            }

            $hydrated[$className] = $mapper->map($entityData);
        }

        return $hydrated;
    }
}