<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;

abstract class AbstractAggregateRootMapper implements PrimaryMapper
{
    use MapperTrait;

    public function hydrate(Extract $extract, string $stateHash): object
    {
        $object = $this->instantiateAugmentedObject($stateHash);

       $this->hydrationClosure()->bindTo($object, $this->applicableFor())($extract);

        return $object;
    }
}