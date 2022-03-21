<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Domain\Quest\Quest;
use RiverRing\Quest\Domain\Quest\QuestId;

final class QuestRepository extends Repository
{
    protected function specification(): AggregateRootSpecification
    {
        return new AggregateRootSpecification(
            Quest::class,
            'id',
            fn (string $idValue) => QuestId::fromString($idValue)
        );
    }

    protected function findEntities(QuestId $id): array
    {
        return [
            Media::class =>
                $this->find('SELECT * FROM quest_media where quest_id = :id', ['id' => $id->toString()]),
        ];
    }

    public function findById(QuestId $id): ?Quest
    {
        return $this->aggregateOne(
            $this->findOne('SELECT * FROM quests where id = :id', ['id' => $id->toString()])
        );
    }

    public function store(Quest $quest): void
    {

    }
}