<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;
use ReflectionClass;
use RuntimeException;

abstract class AbstractMapper implements Mapper
{
    protected function instantiateAugmentedObject(string $stateHash): object
    {
        $objectClassName = $this->applicableFor();
        if (! class_exists($objectClassName)) {
            throw new RuntimeException(sprintf('"%" not valid class name', $objectClassName));
        }

        eval(
            sprintf(
                '$instance = new class (\'%s\') extends %s { use %s; };',
                $stateHash,
                $objectClassName,
                MetaDataTrait::class
            )
        );

        /** @noinspection PhpUndefinedVariableInspection */
        return $instance;
    }

    protected function calculateStateHash(array $data): string
    {
        ksort($data);

        return md5(json_encode($data));
    }
}