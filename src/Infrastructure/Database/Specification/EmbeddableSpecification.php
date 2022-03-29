<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Specification;

final class EmbeddableSpecification
{
    private string $className;
    private string $prefix;

    /**
     * @param class-string $className
     */
    public function __construct(string $className, string $prefix)
    {
        $this->className = $className;
        $this->prefix = $prefix;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function prefix(): string
    {
        return $this->prefix;
    }
}