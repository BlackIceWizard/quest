<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use InvalidArgumentException;
use RiverRing\Quest\Domain\AggregateRootId;

class PrimaryKeySpecification
{
    private string $fieldName;
    private string $valueObjectClassName;

    /**
     * @param class-string<AggregateRootId> $valueObjectClassName
     */
    public function __construct(string $fieldName, string $valueObjectClassName)
    {
        if(!is_subclass_of($valueObjectClassName, AggregateRootId::class)) {
            throw new InvalidArgumentException(sprintf('valueObjectClassName must be subclass name of %s', AggregateRootId::class));
        }
        $this->fieldName = $fieldName;
        $this->valueObjectClassName = $valueObjectClassName;
    }

    public function fieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @return class-string<AggregateRootId>
     */
    public function className(): string
    {
        return $this->valueObjectClassName;
    }
}