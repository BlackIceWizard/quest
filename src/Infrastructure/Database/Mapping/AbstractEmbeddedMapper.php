<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;

abstract class AbstractEmbeddedMapper implements EmbeddedMapper
{
    use MapperTrait;

    public function hydrate(Extract $extract): object
    {
        $object = $this->instantiateAsIs();

        $this->hydrationClosure()->bindTo($object, $this->applicableFor())($extract);

        return $object;
    }
}