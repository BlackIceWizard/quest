<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Mapping;

interface Augmentation
{
    public function stateHash(): string;
}