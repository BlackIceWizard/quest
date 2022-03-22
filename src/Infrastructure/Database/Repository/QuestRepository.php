<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use JetBrains\PhpStorm\Pure;
use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Domain\Quest\Quest;
use RiverRing\Quest\Domain\Quest\QuestId;
use RiverRing\Quest\Infrastructure\Database\Repository\Specification\AggregateRootSpecification;
use RiverRing\Quest\Infrastructure\Database\Repository\Specification\PluralEntitySpecification;

final class QuestRepository extends Repository
{
    #[Pure]
    protected function specification(): AggregateRootSpecification
    {
        return new AggregateRootSpecification(
            Quest::class,
            'id',
            [
                new PluralEntitySpecification(Media::class, 'quest_media', 'quest_id'),
            ]
        );
    }

    public function findById(QuestId $id): ?Quest
    {
        return $this->aggregateOne(
            $this->findOne('SELECT * FROM quests where id = :id', ['id' => $id->toString()])
        );
    }
}