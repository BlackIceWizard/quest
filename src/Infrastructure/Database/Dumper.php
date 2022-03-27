<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database;

use RiverRing\Quest\Infrastructure\Database\Mapping\Extract;
use RiverRing\Quest\Infrastructure\Database\Mapping\MapperRegistry;
use RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation\RawData;
use RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation\Record;
use RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation\RecordStatus;
use RiverRing\Quest\Infrastructure\Database\Specification\AggregateRootSpecification;
use RiverRing\Quest\Infrastructure\Database\Specification\EmbeddableSpecification;
use RuntimeException;

class Dumper
{
    private MapperRegistry $mappers;

    public function __construct(MapperRegistry $mappers)
    {
        $this->mappers = $mappers;
    }

    public function dump(AggregateRootSpecification $specification, object $aggregateRoot): RawData
    {
        $aggregateRootMapper = $this->mappers->aggregateRootMapper($specification->className());
        $aggregateRootExtract = $aggregateRootMapper->dehydrate($aggregateRoot);

        $this->checkEmbeddableList(
            get_class($aggregateRootMapper),
            $aggregateRootExtract->embeddable(),
            $aggregateRootMapper->embeddable()
        );

        return new RawData(
            new Record(
                array_merge(
                    $aggregateRootExtract->data(),
                    $this->extractEmbeddable($aggregateRootExtract->embeddable(), $aggregateRootMapper->embeddable())
                )
            ),
            []
        );
    }

    private function determineLiveCycleStatus(
        object $object,
        Extract $objectExtract,
        array $embeddable,
        array $embeddableExtract
    ): RecordStatus {

    }

    private function checkEmbeddableList(string $mapperClassName, array $embeddableList, array $specifications): void
    {
        $embeddableCount = count($embeddableList);
        $specificationCount = count($specifications);

        if ($specificationCount !== $embeddableCount) {
            throw new RuntimeException(
                sprintf(
                    'Embeddable count (%d) no equals to embeddable specification count (%d) in extract of mapper %s',
                    $embeddableCount,
                    $specificationCount,
                    $mapperClassName
                )
            );
        }

        foreach ($specifications as $embeddableKey => $specification) {
            if (! isset($embeddableList[$embeddableKey])) {
                throw new RuntimeException(
                    sprintf(
                        'Embeddable %s not found in extract of mapper %s',
                        $embeddableKey,
                        $mapperClassName
                    )
                );
            }
        }
    }

    /**
     * @param object[] $embeddableList
     * @param EmbeddableSpecification[] $specifications
     */
    private function extractEmbeddable(array $embeddableList, array $specifications): array
    {
        foreach ($specifications as $embeddableKey => $specification) {
            if (! isset($embeddableList[$embeddableKey])) {
                throw new RuntimeException('');
            }
        }
    }

    private function addDataKeyPrefix(array $data, string $prefix): array
    {
        return array_combine(
            array_map(
                fn(string $key): string => $prefix . $key,
                array_keys($data)
            ),
            array_values($data)
        );
    }
}