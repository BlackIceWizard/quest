<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository\Specification;

final class AggregateRootSpecification
{
    private string $className;
    private string $primaryKeyField;
    private array $entitySpecifications;

    /**
     * @param class-string $className
     * @param EntitySpecification[] $entitySpecifications
     */
    public function __construct(string $className, string $primaryKeyField, array $entitySpecifications = [])
    {
        $this->className = $className;
        $this->primaryKeyField = $primaryKeyField;
        $this->entitySpecifications = $entitySpecifications;
    }

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
    }

    public function primaryKeyField(): string
    {
        return $this->primaryKeyField;
    }

    /**
     * @return EntitySpecification[]
     */
    public function entitySpecifications(): array
    {
        return $this->entitySpecifications;
    }


}