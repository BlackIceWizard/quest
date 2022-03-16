<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use Ramsey\Uuid\UuidInterface;
use RiverRing\Quest\Domain\Quest\Quest;

class QuestRepository extends Repository
{
    public function findById(UuidInterface $id): ?Quest
    {
        return $this->hydrateOne(
            Quest::class,
            $this->findOne(
                'SELECT * FROM quests where id = :id',
                ['id' => $id->toString()]
            )
        );
    }
}