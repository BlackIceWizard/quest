<?php
declare(strict_types=1);

namespace RiverRing\Quest\Domain;

interface AggregateRootId
{
    public function toString(): string;

    public static function fromString(string $aggregateRootId): self;
}