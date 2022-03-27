<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Specification;

final class EmbeddableSpecification
{
    private string $class;
    private string $prefix;

    /**
     * @param class-string $class
     */
    public function __construct(string $class, string $prefix)
    {
        $this->class = $class;
        $this->prefix = $prefix;
    }

    public function class(): string
    {
        return $this->class;
    }

    public function prefix(): string
    {
        return $this->prefix;
    }
}