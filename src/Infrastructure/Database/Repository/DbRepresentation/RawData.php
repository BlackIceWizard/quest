<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation;

class RawData
{
    /** @var Record[]|Record[][] */
    private array $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    /**
     * @param class-string $className
     * @return Record|Record[]|null
     */
    public function byClassname(string $className): null|Record|array
    {
        return $this->records[$className];
    }
}