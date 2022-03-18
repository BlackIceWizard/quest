<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use RuntimeException;

class MapperRegistry
{
    /** @var Mapper[] */
    private array $mappers = [];

    /**
     * @param Mapper[] $mappers
     */
    public function __construct(iterable $mappers)
    {
        foreach ($mappers as $mapper) {
            $this->mappers[$mapper->applicableFor()] = $mapper;
        }
    }

    /**
     * @param class-string $className
     */
    public function byClassName(string $className): EntityMapper|AggregateRootMapper
    {
        if (! isset($this->mappers[$className])) {
            throw new RuntimeException(sprintf('No mapper registered for class %s', $className));
        }

        return $this->mappers[$className];
    }
}