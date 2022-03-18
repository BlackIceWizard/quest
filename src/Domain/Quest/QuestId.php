<?php
declare(strict_types=1);

namespace RiverRing\Quest\Domain\Quest;

use RiverRing\Quest\Domain\AggregateRootId;
use RiverRing\Quest\Domain\AggregateRootIdTrait;

class QuestId implements AggregateRootId
{
    use AggregateRootIdTrait;
}