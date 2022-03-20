<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;
use ReflectionClass;
use ReflectionException;

abstract class AbstractAggregateRootMapper extends AbstractMapper implements AggregateRootMapper, EmbeddedAwareMapper
{
    /**
     * @throws ReflectionException
     */
    public function map(array $data, array $entities, array $embeddable): object
    {
        $object = $this
            ->initReflector()
            ->newInstanceWithoutConstructor();

        Closure::bind($this->hydrationClosure(), $object, $object)($data, $entities, $embeddable);

        return $object;
    }
}