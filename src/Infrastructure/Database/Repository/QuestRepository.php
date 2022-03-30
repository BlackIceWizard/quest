<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use JetBrains\PhpStorm\Pure;
use RiverRing\OwlOrm\Repository\Repository;
use RiverRing\OwlOrm\Specification\AggregateRootSpecification;
use RiverRing\OwlOrm\Specification\PluralEntitySpecification;
use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Domain\Quest\Quest;
use RiverRing\Quest\Domain\Quest\QuestId;

final class QuestRepository extends Repository
{
    #[Pure]
    protected function specification(): AggregateRootSpecification
    {
        return AggregateRootSpecification::prevalent(Quest::class, 'quests', [
            PluralEntitySpecification::prevalent(Media::class, 'quest_media', 'quest_id'),
        ]);
    }

    public function findById(QuestId $id): ?Quest
    {
        return $this->aggregateOne(
            $this->findOne('SELECT * FROM quests where id = :id', ['id' => $id->toString()])
        );
    }
}