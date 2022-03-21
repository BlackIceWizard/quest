<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use Closure;

final class AggregateRootSpecification
{
    private string $aggregateRootClassName;
    private string $primaryKeyFieldName;
    private Closure $aggregateRootIdFactory;

    /**
     * @param class-string $aggregateRootClassName
     */
    public function __construct(string $aggregateRootClassName, string $primaryKeyFieldName, Closure $aggregateRootIdFactory)
    {
        $this->aggregateRootClassName = $aggregateRootClassName;
        $this->primaryKeyFieldName = $primaryKeyFieldName;
        $this->aggregateRootIdFactory = $aggregateRootIdFactory;
    }

    /**
     * @return class-string
     */
    public function aggregateRootClassName(): string
    {
        return $this->aggregateRootClassName;
    }

    public function primaryKeyFieldName(): string
    {
        return $this->primaryKeyFieldName;
    }

    public function aggregateRootIdFactory(): Closure
    {
        return $this->aggregateRootIdFactory;
    }


}