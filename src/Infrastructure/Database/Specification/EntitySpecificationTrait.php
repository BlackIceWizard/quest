<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Specification;

use JetBrains\PhpStorm\Pure;

trait EntitySpecificationTrait
{
    private string $className;
    private string $table;
    private string $primaryKeyField;
    private string $referencedField;

    /**
     * @param class-string $className
     */
    public function __construct(string $className, string $table, string $primaryKeyField, string $referencedField)
    {
        $this->className = $className;
        $this->table = $table;
        $this->primaryKeyField = $primaryKeyField;
        $this->referencedField = $referencedField;
    }

    #[Pure]
    public static function prevalent (string $className, string $table, string $referencedField): self
    {
        return new self($className, $table, 'id', $referencedField);
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

    public function referencedField(): string
    {
        return $this->referencedField;
    }
}