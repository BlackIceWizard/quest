<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use JetBrains\PhpStorm\Pure;
use RiverRing\Quest\Domain\Quest\Media;
use RiverRing\Quest\Domain\Quest\Quest;
use RiverRing\Quest\Domain\Quest\QuestId;

final class QuestRepository extends Repository
{
    #[Pure]
    protected function specification(): AggregateRootSpecification
    {
        return new AggregateRootSpecification(
            Quest::class,
            'id',
            fn(string $idValue) => QuestId::fromString($idValue)
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
        $data = $this->dump($quest);
        
        $questData = $data[Quest::class];

        $this->execute(
            sprintf(
                'INSERT INTO quests (%s)
        VALUES(%s) 
        ON CONFLICT (id) 
        DO
            UPDATE SET email = EXCLUDED.email ||',
                $this->formatFieldNamesToStore($questData),
                $this->formatPlaceholdersToStore($questData)
            ),
            $questData
        );
    }
    
    private function formatFieldNamesToStore(mixed $questData): string
    {
        return implode(
            ', ',
            array_map(
                fn(string $key) => "'" . $key . "'",
                array_keys($questData)
            )
        );
    }

    private function formatPlaceholdersToStore(mixed $questData): string
    {
        return implode(
            ', ',
            array_map(
                fn(string $key) => ":" . $key,
                array_keys($questData)
            )
        );
    }
}