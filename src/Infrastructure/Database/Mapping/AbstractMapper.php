<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;
use ReflectionClass;

abstract class AbstractMapper implements Mapper
{
    private ?ReflectionClass $reflector = null;

    protected function initReflector(): ReflectionClass
    {
        if ($this->reflector === null) {
            $this->reflector = new ReflectionClass($this->applicableFor());
        }

        return $this->reflector;
    }
}