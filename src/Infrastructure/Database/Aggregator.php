<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database;

use Iterator;
use RiverRing\Quest\Infrastructure\Database\Mapping\Embeddable\EmbeddableSpecification;
use RiverRing\Quest\Infrastructure\Database\Mapping\MapperRegistry;

/**
 * @template T
 */
final class Aggregator
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
        $aggregateRootMapper = $this->mappers->aggregateRootMapper($aggregateRootClassName);

        return $aggregateRootMapper->map(
            $aggregateRootData,
            $this->mapEntities($entitiesData),
            $this->mapEmbeddable(
                $this->excludeEmbeddableFields($aggregateRootData, $aggregateRootMapper->embeddable()),
                $aggregateRootMapper->embeddable()
            )
        );
    }

    private function mapEntities(array $entitiesData): array
    {
        $hydrated = [];
        foreach ($entitiesData as $className => $entityData) {
            if ($entityData === null) {
                $hydrated[$className] = null;
                continue;
            }

            $mapper = $this->mappers->entityMapper($className);

            if ($entityData instanceof Iterator) {
                $hydrated[$className] = [];
                foreach ($entityData as $entityListItemData) {
                    $hydrated[$className][] = $mapper->map(
                        $this->excludeEmbeddableFields($entityListItemData, $mapper->embeddable()),
                        $this->mapEmbeddable($entityListItemData, $mapper->embeddable())
                    );
                }
                continue;
            }

            $hydrated[$className] = $mapper->map(
                $this->excludeEmbeddableFields($entityData, $mapper->embeddable()),
                $this->mapEmbeddable($entityData, $mapper->embeddable())
            );
        }

        return $hydrated;
    }

    /**
     * @param EmbeddableSpecification[] $specifications
     */
    private function mapEmbeddable(array $data, array $specifications): array
    {
        $embeddable = [];

        foreach ($specifications as $key => $specification) {
            $mapper = $this->mappers->embeddedMapper($specification->class());
            $embeddable[$key] = $mapper->map(
                $this->filterDataContains($data, $specification->prefix()),
                $specification->prefix()
            );
        }

        return $embeddable;
    }

    /**
     * @param EmbeddableSpecification[] $specifications
     */
    private function excludeEmbeddableFields(array $data, array $specifications): array
    {
        return $this->excludeFields(
            $data,
            ...array_map(
                fn (EmbeddableSpecification $specification) => $specification->prefix(),
                $specifications
            )
        );
    }

    private function filterDataContains(array $data, ...$withKeyPrefixes): array
    {
        return $this->filterData($data, $withKeyPrefixes, true);
    }

    private function excludeFields(array $data, ...$withKeyPrefixes): array
    {
        return $this->filterData($data, $withKeyPrefixes, false);
    }

    private function filterData(array $data, array $keyPrefixes, bool $contains): array
    {
        foreach ($keyPrefixes as $keyPrefix) {
            $data = array_filter(
                $data,
                fn (string $key) => str_starts_with($key, $keyPrefix) ? $contains : ! $contains,
                ARRAY_FILTER_USE_KEY
            );
        }

        return $data;
    }
}