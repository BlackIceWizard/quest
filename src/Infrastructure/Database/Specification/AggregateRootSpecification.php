<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Specification;

final class AggregateRootSpecification
{
    private string $className;
    private string $table;
    private string $primaryKeyField;
    private array $entitySpecifications;

    /**
     * @param class-string $className
     * @param EntitySpecification[] $entitySpecifications
     */
    public function __construct(string $className, string $table, string $primaryKeyField, array $entitySpecifications = [])
    {
        $this->className = $className;
        $this->table = $table;
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
    
    public function table(): string
    {
        return $this->table;
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