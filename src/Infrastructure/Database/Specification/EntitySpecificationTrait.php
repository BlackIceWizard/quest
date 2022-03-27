<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Specification;

trait EntitySpecificationTrait
{
    private string $className;
    private string $tableName;
    private string $referencedField;

    /**
     * @param class-string $className
     */
    public function __construct(string $className, string $tableName, string $referencedField)
    {
        $this->className = $className;
        $this->tableName = $tableName;
        $this->referencedField = $referencedField;
    }

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
    }

    public function tableName(): string
    {
        return $this->tableName;
    }

    public function referencedField(): string
    {
        return $this->referencedField;
    }
}