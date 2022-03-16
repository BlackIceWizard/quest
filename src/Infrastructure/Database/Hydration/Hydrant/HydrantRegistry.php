<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Hydration\Hydrant;

use RuntimeException;

class HydrantRegistry
{
    /** @var Hydrant[] */
    private array $hydrants = [];

    /**
     * @param Hydrant[] $hydrants
     */
    public function __construct(iterable $hydrants)
    {
        foreach ($hydrants as $hydrant) {
            $this->hydrants[$hydrant->applicableFor()] = $hydrant;
        }
    }

    /**
     * @param class-string $className
     */
    public function byClassName(string $className): Hydrant  {
        if (! isset($this->hydrants[$className])) {
            throw new RuntimeException(sprintf('No hydrant registered for class %s', $className));
        }

        return $this->hydrants[$className];
    }
}