<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Repository;

use Closure;
use Iterator;
use PDO;
use ReflectionClass;
use ReflectionException;
use RiverRing\Quest\Infrastructure\Database\Hydration\Hydrant\HydrantRegistry;
use RiverRing\Quest\Infrastructure\Database\PdoProvider;

abstract class Repository
{
    protected PDO $pdo;

    /** @var ReflectionClass[] */
    private array $classReflectors = [];

    private HydrantRegistry $hydrants;

    public function __construct(PdoProvider $pdoProvider, HydrantRegistry $hydrants)
    {
        $this->pdo = $pdoProvider->provide();
        $this->hydrants = $hydrants;
    }

    protected function findOne($sql, $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($row = $stmt->fetch()) {
            return $row;
        }

        return null;
    }

    protected function find($sql, $params = []): Iterator
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->getIterator();
    }

    private function reflectorByClassName(string $className): ReflectionClass
    {
        if (! isset($this->classReflectors[$className])) {
            $this->classReflectors[$className] = new ReflectionClass($className);
        }

        return $this->classReflectors[$className];
    }

    /**
     * @template T
     * @param class-string<T> $target
     * @return ?T
     *
     * @throws ReflectionException
     */
    protected function hydrateOne(string $target, ?array $data): ?object
    {
        if ($data === null) {
            return null;
        }

        $reflector = $this->reflectorByClassName($target);
        $object = $reflector->newInstanceWithoutConstructor();
        $hydrant = $this->hydrants->byClassName($target);

        Closure::bind($hydrant->toClosure(), $object, $object)($data);

        return $object;
    }

    /**
     * @template T
     * @param class-string<T> $target
     * @return Iterator<T>
     *
     * @throws ReflectionException
     */
    protected function hydrateAll(string $target, Iterator $listData): Iterator
    {
        foreach ($listData as $itemData) {
            yield $this->hydrateOne($target, $itemData);
        }
    }

    protected function addHydrant(string $forClass, callable $hydrant)
    {
        $this->hydrants[$forClass] = $hydrant;
    }
}