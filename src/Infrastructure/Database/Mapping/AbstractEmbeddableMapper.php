<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;

abstract class AbstractEmbeddableMapper extends AbstractMapper implements EmbeddableMapper
{
    public function map(array $data, string $prefix): object
    {
        $object = $this->instantiateAugmentedObject($this->calculateStateHash($data));

        Closure::bind($this->hydrationClosure(), $object, $object)($data, $prefix);

        return $object;
    }
}