<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;
use ReflectionClass;

abstract class AbstractEntityMapper implements EntityMapper
{
    private ?ReflectionClass $reflector = null;

    private function initReflector(): ReflectionClass
    {
        if ($this->reflector === null) {
            $this->reflector = new ReflectionClass($this->applicableFor());
        }

        return $this->reflector;
    }


    public function map(array $data): object
    {
        $object = $this
            ->initReflector()
            ->newInstanceWithoutConstructor();

        Closure::bind($this->hydrationClosure(), $object, $object)($data);

        return $object;
    }
}