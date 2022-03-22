<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Dbal\Driver;

use Iterator;
use JetBrains\PhpStorm\Pure;
use PDO;
use RiverRing\Quest\Infrastructure\Database\PdoProvider;

class PostgresDriver implements Driver
{
    private PDO $pdo;

    #[Pure]
    public function __construct(PdoProvider $pdoProvider)
    {
        $this->pdo = $pdoProvider->provide();
    }

    public function execute($sql, $params = []): void
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function findOne($sql, $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($row = $stmt->fetch()) {
            return $row;
        }

        return null;
    }

    public function find($sql, $params = []): Iterator
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->getIterator();
    }

    public function findEntitySet(string|int $aggregateRootId, string $entityTable, string $referencedFieldName): Iterator
    {
        return $this->find(
            sprintf(
                'SELECT * FROM %s where %s = :aggregate_root_id',
                $entityTable,
                $referencedFieldName
            ),
            ['aggregate_root_id' => $aggregateRootId]
        );
    }

    public function findEntity(string|int $aggregateRootId, string $entityTable, string $referencedFieldName): ?array
    {
        return $this->findOne(
            sprintf(
                'SELECT * FROM %s where %s = :aggregate_root_id limit 1',
                $entityTable,
                $referencedFieldName
            ),
            ['aggregate_root_id' => $aggregateRootId]
        );
    }

    /*
     public function store(Quest $quest): void
    {
        $data = $this->dump($quest);

        $questData = $data[Quest::class];

        $this->driver->execute(
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
    */
}