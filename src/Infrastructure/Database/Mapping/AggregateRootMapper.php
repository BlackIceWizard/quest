<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

interface AggregateRootMapper extends Mapper
{
    public function map(array $data, array $entities, array $embeddable): object;
}