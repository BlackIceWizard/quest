<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database;

use InvalidArgumentException;
use RiverRing\Quest\Infrastructure\Database\Mapping\PrimaryMapper;
use RiverRing\Quest\Infrastructure\Database\Mapping\Extract;
use RiverRing\Quest\Infrastructure\Database\Mapping\MapperRegistry;
use RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation\RawData;
use RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation\Record;
use RiverRing\Quest\Infrastructure\Database\Specification\AggregateRootSpecification;
use RiverRing\Quest\Infrastructure\Database\Specification\EmbeddableSpecification;
use RiverRing\Quest\Infrastructure\Database\Specification\EntitySpecification;
use RiverRing\Quest\Infrastructure\Database\Specification\PluralEntitySpecification;
use RiverRing\Quest\Infrastructure\Database\Specification\SingleEntitySpecification;

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

    public function aggregate(AggregateRootSpecification $specification, RawData $rawData): object
    {
        $aggregateRootMapper = $this->mappers->aggregateRootMapper($specification->className());
        $aggregateRootRecord = $rawData->byClassname($specification->className());
        $aggregateRootData = $aggregateRootRecord->data();

        return $aggregateRootMapper->hydrate(
            Extract::ofAggregateRoot(
                $this->excludeEmbeddableFields($aggregateRootData, $aggregateRootMapper->embeddable()),
                $this->hydrateAllEntities($specification->entitySpecifications(), $rawData),
                $this->hydrateEmbeddable(
                    $this->excludeNotEmbeddableFields($aggregateRootMapper->embeddable(), $aggregateRootData),
                    $aggregateRootMapper->embeddable()
                )
            ),
            $aggregateRootRecord->stateHash()
        );
    }

    /**
     * @param EntitySpecification[] $specifications
     * @return object[]
     */
    private function hydrateAllEntities(array $specifications, RawData $rawData): array
    {
        $hydrated = [];
        foreach ($specifications as $specification) {
            $className = $specification->className();
            $mapper = $this->mappers->entityMapper($className);

            $hydrated[$className] = match (true) {
                $specification instanceof SingleEntitySpecification => $this->hydrateSingleEntity($mapper, $rawData->byClassname($className)),
                $specification instanceof PluralEntitySpecification => $this->hydrateMultipleEntities($mapper, $rawData->byClassname($className)),
                default => throw new InvalidArgumentException(sprintf('Unexpected entity specification class %s', get_class($specification))),
            };
        }

        return $hydrated;
    }

    private function hydrateSingleEntity(PrimaryMapper $mapper, ?Record $record): ?object
    {
        if ($record === null) {
            return null;
        }

        $recordData = $record->data();
        return $mapper->hydrate(
            Extract::ofEntity(
                $this->excludeEmbeddableFields($recordData, $mapper->embeddable()),
                $this->hydrateEmbeddable($recordData, $mapper->embeddable())
            ),
            $record->stateHash()
        );
    }

    /**
     * @param Record[] $records
     * @return object[]
     */
    private function hydrateMultipleEntities(PrimaryMapper $mapper, array $records): array
    {
        return array_map(
            function (Record $record) use ($mapper): object {
                $recordData = $record->data();
                return $mapper->hydrate(
                    Extract::ofEntity(
                        $this->excludeEmbeddableFields($recordData, $mapper->embeddable()),
                        $this->hydrateEmbeddable($recordData, $mapper->embeddable())
                    ),
                    $record->stateHash()
                );
            },
            $records
        );
    }

    /**
     * @param EmbeddableSpecification[] $specifications
     */
    private function hydrateEmbeddable(array $data, array $specifications): array
    {
        $embeddable = [];

        foreach ($specifications as $key => $specification) {
            $mapper = $this->mappers->embeddedMapper($specification->class());
            $embeddable[$key] = $mapper->hydrate(
                Extract::ofEmbeddable(
                    $this->removeDataKeyPrefix(
                        $this->filterDataByPrefix($data, $specification->prefix()),
                        $specification->prefix()
                    )
                )
            );
        }

        return $embeddable;
    }

    /**
     * @param EmbeddableSpecification[] $specifications
     */
    private function excludeNotEmbeddableFields(array $specifications, array $data): array
    {
        return $this->filterDataByPrefix(
            $data,
            ...array_map(
                fn(EmbeddableSpecification $specification) => $specification->prefix(),
                $specifications
            )
        );
    }

    /**
     * @param EmbeddableSpecification[] $specifications
     */
    private function excludeEmbeddableFields(array $data, array $specifications): array
    {
        return $this->excludeFieldsByPrefix(
            $data,
            ...array_map(
                fn(EmbeddableSpecification $specification) => $specification->prefix(),
                $specifications
            )
        );
    }

    private function removeDataKeyPrefix(array $data, string $prefix): array
    {
        return array_combine(
            array_map(
                fn(string $key): string => substr($key, strlen($prefix)),
                array_keys($data)
            ),
            array_values($data)
        );
    }

    private function filterDataByPrefix(array $data, ...$prefixes): array
    {
        return $this->filterData($data, $prefixes, true);
    }

    private function excludeFieldsByPrefix(array $data, ...$prefixes): array
    {
        return $this->filterData($data, $prefixes, false);
    }

    private function filterData(array $data, array $keyPrefixes, bool $contains): array
    {
        foreach ($keyPrefixes as $keyPrefix) {
            $data = array_filter(
                $data,
                fn(string $key) => str_starts_with($key, $keyPrefix) ? $contains : ! $contains,
                ARRAY_FILTER_USE_KEY
            );
        }

        return $data;
    }
}