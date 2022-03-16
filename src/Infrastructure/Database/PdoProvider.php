<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database;

use PDO;
use PDOException;

class PdoProvider
{
    private PDO $pdo;

    public function __construct(
        string $host,
        string $port,
        string $db,
        string $user,
        string $pass
    ) {
        $dsn = "pgsql:host=$host;port=$port;dbname=$db";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function provide(): PDO {
        return $this->pdo;
    }
}