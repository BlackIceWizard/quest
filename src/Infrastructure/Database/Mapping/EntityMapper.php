<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;

interface EntityMapper extends Mapper
{
    public function map(array $data): object;
}