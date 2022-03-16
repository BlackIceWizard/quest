<?php
declare(strict_types=1);

namespace RiverRing\Quest\Infrastructure\Database\Hydration\Hydrant\Quest;

use Closure;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use RiverRing\Quest\Domain\Quest\Quest;
use RiverRing\Quest\Infrastructure\Database\Hydration\Hydrant\Hydrant;

class QuestHydrant implements Hydrant
{
    /**
     * @return class-string
     */
    public function applicableFor(): string
    {
        return Quest::class;
    }

    public function toClosure(): Closure
    {
        return function (array $data) {
            /** @var Quest $this */
            $this->id = Uuid::fromString($data['id']);
            $this->name = $data['name'];
            $this->createdAt = new DateTimeImmutable($data['created_at']);
        };
    }
}