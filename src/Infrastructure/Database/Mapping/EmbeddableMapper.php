<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;

interface EmbeddableMapper extends Mapper
{
    public function map(array $data, string $prefix): object;
}