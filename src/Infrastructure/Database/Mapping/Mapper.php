<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;

interface Mapper
{
    /**
     * @return class-string
     */
    public function applicableFor(): string;

    public function hydrationClosure(): Closure;

    public function dehydrationClosure(): Closure;
}