<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use Closure;
use RiverRing\Quest\Infrastructure\Database\Mapping\Embeddable\EmbeddableSpecification;

interface EmbeddedAwareMapper
{
    /**
     * @return EmbeddableSpecification[]
     */
    public function embeddable(): array;
}