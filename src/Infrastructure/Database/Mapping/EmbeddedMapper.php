<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

interface EmbeddedMapper extends Mapper
{
    public function hydrate(Extract $extract): object;
}