<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;
use ReflectionClass;
use ReflectionException;

abstract class AbstractAggregateRootMapper implements AggregateRootMapper
{
    private ?ReflectionClass $reflector = null;

    private function initReflector(): ReflectionClass
    {
        if ($this->reflector === null) {
            $this->reflector = new ReflectionClass($this->applicableFor());
        }

        return $this->reflector;
    }


    /**
     * @throws ReflectionException
     */
    public function map(array $data, array $entities): object
    {
        $object = $this
            ->initReflector()
            ->newInstanceWithoutConstructor();

        Closure::bind($this->hydrationClosure(), $object, $object)($data, $entities);

        return $object;
    }
}