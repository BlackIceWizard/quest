<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;

abstract class AbstractEntityMapper implements PrimaryMapper
{
    use MapperTrait;

    public function hydrate(Extract $extract, string $stateHash): object
    {
        $object = $this->instantiateAugmentedObject($stateHash);

        Closure::bind($this->hydrationClosure(), $object, $object)($extract);

        return $object;
    }
}