<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use RuntimeException;
use Symfony\Component\Routing\Exception\InvalidArgumentException;

final class MapperRegistry
{
    /** @var AggregateRootMapper[] */
    private array $aggregateRootMappers = [];

    /** @var EntityMapper[] */
    private array $entityMappers = [];

    /** @var EmbeddableMapper[] */
    private array $embeddedMappers = [];

    /**
     * @param Mapper[] $mappers
     */
    public function __construct(iterable $mappers)
    {
        foreach ($mappers as $mapper) {
            switch (true) {
                case $mapper instanceof AggregateRootMapper:
                    $this->aggregateRootMappers[$mapper->applicableFor()] = $mapper;
                    break;
                case $mapper instanceof EntityMapper:
                    $this->entityMappers[$mapper->applicableFor()] = $mapper;
                    break;
                case $mapper instanceof EmbeddableMapper:
                    $this->embeddedMappers[$mapper->applicableFor()] = $mapper;
                    break;
                default:
                    throw new InvalidArgumentException(sprintf('Unexpected mapper class %s', $mappers::class));
            }
        }
    }

    /**
     * @param class-string $className
     */
    public function aggregateRootMapper(string $className): AggregateRootMapper
    {
        if (! isset($this->aggregateRootMappers[$className])) {
            throw new RuntimeException(sprintf('No aggregate root mapper registered for class %s', $className));
        }

        return $this->aggregateRootMappers[$className];
    }

    /**
     * @param class-string $className
     */
    public function entityMapper(string $className): EntityMapper
    {
        if (! isset($this->entityMappers[$className])) {
            throw new RuntimeException(sprintf('No entity mapper registered for class %s', $className));
        }

        return $this->entityMappers[$className];
    }

    /**
     * @param class-string $className
     */
    public function embeddedMapper(string $className): EmbeddableMapper
    {
        if (! isset($this->embeddedMappers[$className])) {
            throw new RuntimeException(sprintf('No embedded mapper registered for class %s', $className));
        }

        return $this->embeddedMappers[$className];
    }
}