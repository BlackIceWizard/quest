<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Dbal\Driver;

use Exception;
use Iterator;
use JetBrains\PhpStorm\Pure;
use PDO;
use RiverRing\Quest\Infrastructure\Database\PdoProvider;
use RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation\Record;
use RiverRing\Quest\Infrastructure\Database\Repository\DbRepresentation\RecordStatus;

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

    public function transactional(callable $operation): void
    {
        $this->pdo->beginTransaction();

        try {
            $operation();
        } catch (Exception $e) {
            $this->pdo->rollBack();

            throw $e;
        }

        $this->pdo->commit();
    }

    public function store(string $table, string $primaryKeyField, Record $record): void
    {
        switch ($record->status()) {
            case RecordStatus::New:
                $this->addNew($table, $record->data());
                break;
            case RecordStatus::Changed:
                $this->updateExisting($table, $primaryKeyField, $record->data());
                break;
        }
    }

    private function addNew(string $table, array $fields): void
    {
        $this->execute(
            sprintf(
                'INSERT INTO "%s" (%s) VALUES (%s)',
                $table,
                $this->formatFieldNamesToStore($fields),
                $this->formatPlaceholdersToStore($fields)
            ),
            $fields
        );
    }

    private function updateExisting(string $table, string $primaryKeyField, array $fields): void
    {
        $this->execute(
            sprintf(
                'UPDATE "%s" SET (%s) = (%s) WHERE "%s" = :%s_2',
                $table,
                $this->formatFieldNamesToStore($fields),
                $this->formatPlaceholdersToStore($fields),
                $primaryKeyField,
                $primaryKeyField
            ),
            $fields + [$primaryKeyField . '_2' => $fields[$primaryKeyField]]
        );
    }

    private function formatFieldNamesToStore(array $questData): string
    {
        return implode(
            ', ',
            array_map(
                fn(string $key) => '"' . $key . '"',
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