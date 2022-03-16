<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Hydration\Hydrant;

use Closure;

interface Hydrant
{
    /**
     * @return class-string
     */
    public function applicableFor(): string;

    public function toClosure(): Closure;
}