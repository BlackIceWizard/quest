<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;

abstract class AbstractEntityMapper extends AbstractMapper implements EntityMapper, EmbeddedAwareMapper
{
    public function map(array $data, array $embeddable): object
    {
        $object = $this
            ->initReflector()
            ->newInstanceWithoutConstructor();

        Closure::bind($this->hydrationClosure(), $object, $object)($data, $embeddable);

        return $object;
    }
}