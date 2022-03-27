<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

use RiverRing\Quest\Infrastructure\Database\Specification\EmbeddableSpecification;

interface PrimaryMapper extends Mapper
{
    /**
     * @return EmbeddableSpecification[]
     */
    public function embeddable(): array;

    public function hydrate(Extract $extract, string $stateHash): object;
}